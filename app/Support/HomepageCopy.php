<?php

namespace App\Support;

class HomepageCopy
{
    /** @return array<string, array{en: string, ar: string}> */
    public static function defaults(): array
    {
        return [
            'hero_accessible_label' => ['en' => 'Featured architecture work', 'ar' => 'أعمال معمارية مختارة'],
            'hero_eyebrow' => ['en' => 'Independent practice · Cairo', 'ar' => 'استوديو مستقل · القاهرة'],
            'view_projects' => ['en' => 'View projects', 'ar' => 'عرض المشاريع'],
            'get_in_touch' => ['en' => 'Get in touch', 'ar' => 'تواصل معنا'],
            'scroll_to_projects' => ['en' => 'Scroll to selected projects', 'ar' => 'انتقل إلى المشاريع المختارة'],
            'selected_eyebrow' => ['en' => 'Selected work', 'ar' => 'أعمال مختارة'],
            'projects_heading' => ['en' => 'Projects', 'ar' => 'المشاريع'],
            'all_projects' => ['en' => 'All projects', 'ar' => 'كل المشاريع'],
            'empty_projects' => ['en' => 'No homepage projects have been selected yet.', 'ar' => 'لم يتم اختيار مشاريع للصفحة الرئيسية بعد.'],
            'about_link' => ['en' => 'More about the studio', 'ar' => 'المزيد عن الاستوديو'],
            'pages_label' => ['en' => 'Pages', 'ar' => 'الصفحات'],
            'home_link' => ['en' => 'Home', 'ar' => 'الرئيسية'],
            'projects_link' => ['en' => 'Projects', 'ar' => 'المشاريع'],
            'categories_link' => ['en' => 'Categories', 'ar' => 'التصنيفات'],
            'loading_categories' => ['en' => 'Loading categories…', 'ar' => 'جارٍ تحميل التصنيفات…'],
            'no_categories' => ['en' => 'No categories available', 'ar' => 'لا توجد تصنيفات متاحة'],
            'about_nav_link' => ['en' => 'About', 'ar' => 'من نحن'],
            'sign_in_link' => ['en' => 'Sign in', 'ar' => 'تسجيل الدخول'],
            'sign_up_link' => ['en' => 'Sign up', 'ar' => 'إنشاء حساب'],
            'account_link' => ['en' => 'Account', 'ar' => 'الحساب'],
            'sign_out_link' => ['en' => 'Sign out', 'ar' => 'تسجيل الخروج'],
            'preferences_label' => ['en' => 'Preferences', 'ar' => 'التفضيلات'],
            'main_navigation_label' => ['en' => 'Main navigation', 'ar' => 'القائمة الرئيسية'],
            'open_navigation_label' => ['en' => 'Open navigation menu', 'ar' => 'فتح قائمة التنقل'],
            'close_navigation_label' => ['en' => 'Close navigation menu', 'ar' => 'إغلاق قائمة التنقل'],
            'site_preferences_label' => ['en' => 'Site preferences', 'ar' => 'تفضيلات الموقع'],
            'change_language_label' => ['en' => 'Change language', 'ar' => 'تغيير اللغة'],
            'language_toggle_en' => ['en' => 'العربية', 'ar' => 'العربية'],
            'language_toggle_ar' => ['en' => 'English', 'ar' => 'English'],
            'change_theme_label' => ['en' => 'Change color mode', 'ar' => 'تغيير الوضع'],
            'dark_mode_label' => ['en' => 'Dark mode', 'ar' => 'الوضع الداكن'],
            'light_mode_label' => ['en' => 'Light mode', 'ar' => 'الوضع الفاتح'],
            'follow_studio' => ['en' => 'Follow the studio', 'ar' => 'تابع الاستوديو'],
            'social_media_label' => ['en' => 'Social media', 'ar' => 'وسائل التواصل الاجتماعي'],
            'admin_sign_in' => ['en' => 'Administrator sign in', 'ar' => 'دخول الإدارة'],
            'whatsapp_accessible_label' => ['en' => 'Contact on WhatsApp', 'ar' => 'تواصل عبر واتساب'],
            'project_category_fallback' => ['en' => 'Architecture', 'ar' => 'عمارة'],
            'project_location_fallback' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
            'internal_projects_suffix' => ['en' => 'internal projects', 'ar' => 'مشاريع داخلية'],
        ];
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            'hero_accessible_label' => 'Hero image accessibility label',
            'hero_eyebrow' => 'Hero small heading',
            'view_projects' => 'View projects button',
            'get_in_touch' => 'Contact button',
            'scroll_to_projects' => 'Scroll button accessibility label',
            'selected_eyebrow' => 'Selected work small heading',
            'projects_heading' => 'Projects heading',
            'all_projects' => 'All projects button',
            'empty_projects' => 'Empty projects message',
            'about_link' => 'About section link',
            'pages_label' => 'Navigation section heading',
            'home_link' => 'Home navigation link',
            'projects_link' => 'Projects navigation link',
            'categories_link' => 'Categories dropdown label',
            'loading_categories' => 'Categories dropdown loading message',
            'no_categories' => 'Empty categories dropdown message',
            'about_nav_link' => 'About navigation link',
            'sign_in_link' => 'Sign in navigation link',
            'sign_up_link' => 'Sign up navigation link',
            'account_link' => 'Account navigation link',
            'sign_out_link' => 'Sign out navigation button',
            'preferences_label' => 'Preferences section heading',
            'main_navigation_label' => 'Main navigation accessibility label',
            'open_navigation_label' => 'Open navigation accessibility label',
            'close_navigation_label' => 'Close navigation accessibility label',
            'site_preferences_label' => 'Site preferences accessibility label',
            'change_language_label' => 'Language button accessibility label',
            'language_toggle_en' => 'Language button label while the site is in English',
            'language_toggle_ar' => 'Language button label while the site is in Arabic',
            'change_theme_label' => 'Theme button accessibility label',
            'dark_mode_label' => 'Dark mode button label',
            'light_mode_label' => 'Light mode button label',
            'follow_studio' => 'Social links heading',
            'social_media_label' => 'Social links accessibility label',
            'admin_sign_in' => 'Administrator link in footer',
            'whatsapp_accessible_label' => 'WhatsApp button accessibility label',
            'project_category_fallback' => 'Project category fallback',
            'project_location_fallback' => 'Project location fallback',
            'internal_projects_suffix' => 'Subproject count suffix',
        ];
    }

    /** @return array<string, array{en: string, ar: string}> */
    public static function merge(?array $copy): array
    {
        return array_replace_recursive(self::defaults(), $copy ?? []);
    }
}
