<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageCompressor
{
    public static function store(
        UploadedFile $file,
        string $disk = 'public',
        ?string $directory = null,
        ?int $maxWidth = 1920,
        int $quality = 75,
    ): string {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            throw new RuntimeException('GD with WebP support is required to store site images.');
        }

        $imageData = file_get_contents($file->getRealPath());

        if ($imageData === false || $imageData === '') {
            throw new RuntimeException('The uploaded image could not be read.');
        }

        $dimensions = @getimagesizefromstring($imageData);

        if (! $dimensions || ! in_array($dimensions['mime'] ?? '', ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException('Only JPEG, PNG, and WebP images can be stored.');
        }

        [$sourceWidth, $sourceHeight] = $dimensions;

        if ($sourceWidth * $sourceHeight > 80000000) {
            throw new RuntimeException('The uploaded image dimensions are too large.');
        }

        $source = @imagecreatefromstring($imageData);

        if (! $source) {
            throw new RuntimeException('The uploaded image could not be decoded.');
        }

        $target = null;

        try {
            $targetWidth = $maxWidth ? min($sourceWidth, $maxWidth) : $sourceWidth;
            $targetHeight = max(1, (int) round($sourceHeight * ($targetWidth / $sourceWidth)));
            $target = imagecreatetruecolor($targetWidth, $targetHeight);

            if (! $target) {
                throw new RuntimeException('The image could not be prepared for compression.');
            }

            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefill($target, 0, 0, $transparent);

            if (! imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight)) {
                throw new RuntimeException('The image could not be resized.');
            }

            ob_start();
            $encoded = imagewebp($target, null, max(0, min(100, $quality)));
            $contents = ob_get_clean();

            if (! $encoded || ! is_string($contents) || $contents === '') {
                throw new RuntimeException('The image could not be converted to WebP.');
            }

            $directory = trim((string) $directory, '/');
            $path = ($directory !== '' ? $directory.'/' : '').Str::ulid().'.webp';

            if (! Storage::disk($disk)->put($path, $contents)) {
                throw new RuntimeException('The compressed image could not be saved.');
            }

            return $path;
        } finally {
            imagedestroy($source);

            if ($target instanceof \GdImage) {
                imagedestroy($target);
            }
        }
    }
}
