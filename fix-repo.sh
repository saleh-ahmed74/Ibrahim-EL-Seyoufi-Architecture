#!/usr/bin/env bash
# fix-repo.sh
#
# يصلح المشاكل اللي اتكلمنا عليها في الريبو:
# 1) نقل filament/filament من require-dev لـ require
# 2) مسح ملفات الـ debug اللي بتوصل للداتابيز مباشرة
# 3) تصليح .gitignore بتاع الـ frontend عشان .env.local ميترفعش تاني
# 4) إضافة ملف DEPLOYMENT.md فيه أوامر السيرفر الصح
#
# طريقة الاستخدام:
#   1. افتح Terminal في جذر الريبو بتاعك (الفولدر اللي فيه backend/ و frontend/)
#   2. انسخ الملف ده جواه باسم fix-repo.sh
#   3. شغّل: bash fix-repo.sh
#   4. راجع اللي اتغير بـ: git status  و  git diff
#   5. لو مبسوط بالنتيجة: git add -A && git commit -m "fix: production readiness issues"
#   6. git push

set -e  # لو أي خطوة فشلت، السكريبت يوقف بدل ما يكمل ويخرب حاجة

# تأكيد إننا في المكان الصح
if [ ! -d "backend" ] || [ ! -d "frontend" ]; then
    echo "❌ لازم تشغل السكريبت من جذر الريبو (المكان اللي فيه فولدر backend وfolder frontend)."
    exit 1
fi

echo "==> 1) نقل filament/filament من require-dev لـ require في composer.json"
COMPOSER_FILE="backend/composer.json"
if grep -q '"filament/filament"' "$COMPOSER_FILE" 2>/dev/null; then
    if grep -A2 '"require-dev"' "$COMPOSER_FILE" | grep -q "filament/filament"; then
        if command -v php >/dev/null 2>&1; then
            php -r '
                $path = "backend/composer.json";
                $data = json_decode(file_get_contents($path), true);
                if (isset($data["require-dev"]["filament/filament"])) {
                    $data["require"]["filament/filament"] = $data["require-dev"]["filament/filament"];
                    unset($data["require-dev"]["filament/filament"]);
                    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
                    echo "   تم النقل بنجاح (عن طريق PHP).\n";
                }
            '
        elif command -v python3 >/dev/null 2>&1; then
            python3 -c '
import json
path = "backend/composer.json"
with open(path) as f:
    data = json.load(f)
if "filament/filament" in data.get("require-dev", {}):
    data.setdefault("require", {})["filament/filament"] = data["require-dev"]["filament/filament"]
    del data["require-dev"]["filament/filament"]
    with open(path, "w") as f:
        json.dump(data, f, indent=4)
        f.write("\n")
    print("   تم النقل بنجاح (عن طريق Python).")
'
        else
            echo "   ⚠️ مفيش PHP ولا Python متاحين — افتح backend/composer.json يدويًا وانقل السطر:"
            echo '        "filament/filament": "^5.7",'
            echo "      من قسم require-dev لقسم require."
        fi
    else
        echo "   ✅ filament/filament موجودة بالفعل في require — مفيش حاجة تتعمل."
    fi
else
    echo "   ⚠️ ملف composer.json مش موجود أو filament/filament مش موجودة فيه — تجاهلنا الخطوة دي."
fi

echo "==> 2) مسح ملفات الـ debug الخطيرة"
for f in "backend/check_users.php" "backend/update_site_images.php"; do
    if [ -f "$f" ]; then
        git rm -f --cached "$f" 2>/dev/null || true
        rm -f "$f"
        echo "   اتشال: $f"
    else
        echo "   ✅ $f مش موجود أصلاً."
    fi
done

echo "==> 3) تصليح frontend/.gitignore عشان .env.local ميترفعش"
GITIGNORE="frontend/.gitignore"
if [ -f "$GITIGNORE" ]; then
    if grep -q "^\.env\.local/$" "$GITIGNORE"; then
        sed -i.bak 's/^\.env\.local\/$/.env*.local\n.env/' "$GITIGNORE"
        rm -f "$GITIGNORE.bak"
        echo "   اتصلح الـ pattern في .gitignore"
    else
        echo "   ✅ الـ .gitignore متصلح بالفعل أو مختلف عن المتوقع — راجعه يدويًا لو حابب."
    fi
fi
if git ls-files --error-unmatch "frontend/.env.local" >/dev/null 2>&1; then
    git rm --cached "frontend/.env.local"
    echo "   اتشال frontend/.env.local من التتبع (الملف نفسه فاضل عندك لوكال)"
fi

echo "==> 4) إضافة backend/DEPLOYMENT.md"
if [ ! -f "backend/DEPLOYMENT.md" ]; then
cat > backend/DEPLOYMENT.md << 'EOF'
# Deployment checklist

Run these steps on the server every time you deploy.

## 1. Install dependencies (production mode)
composer install --no-dev --optimize-autoloader

## 2. Environment
Copy .env.example to .env and set:
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql

## 3. Cache everything (main performance fix for Filament admin)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:optimize

## 4. Storage & assets
php artisan storage:link
npm run build

## 5. After any future code change
Re-run step 3 — cached config does not auto-update.
EOF
    echo "   اتضاف backend/DEPLOYMENT.md"
else
    echo "   ✅ backend/DEPLOYMENT.md موجود بالفعل."
fi

echo ""
echo "==================================================="
echo "خلصنا. دلوقتي شغل الأوامر دي عشان تشوف وتحفظ التغيير:"
echo ""
echo "   git status"
echo "   git diff"
echo "   git add -A"
echo "   git commit -m \"fix: production readiness issues\""
echo "   git push"
echo "==================================================="
