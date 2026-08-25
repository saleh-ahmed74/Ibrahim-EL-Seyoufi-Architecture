<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageCompressor
{
    /**
     * Compress an uploaded image and store it on the given disk/directory.
     * Returns the stored path, in the same shape Filament's FileUpload expects.
     *
     * @param  UploadedFile  $file
     * @param  string  $disk
     * @param  string|null  $directory
     * @param  int|null  $maxWidth   Resize down if wider than this (null = no resize)
     * @param  int  $quality        JPEG/WebP quality (0-100)
     */
    public static function store(
        UploadedFile $file,
        string $disk = 'public',
        ?string $directory = null,
        ?int $maxWidth = 1920,
        int $quality = 75
    ): string {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        if ($maxWidth && $image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // Encode to WebP for the best size/quality ratio.
        $encoded = $image->toWebp($quality);

        $filename = Str::ulid() . '.webp';
        $path = $directory ? trim($directory, '/') . '/' . $filename : $filename;

        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }
}