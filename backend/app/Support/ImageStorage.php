<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorage
{
    public static function storeDataUrl(?string $value, string $directory, string $baseUrl, ?string $oldValue = null): ?string
    {
        if (!$value || !str_starts_with($value, 'data:image/')) {
            return $value;
        }

        if (!preg_match('/^data:image\/(png|jpe?g|webp|gif);base64,(.+)$/', $value, $matches)) {
            return null;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $binary = base64_decode($matches[2], true);

        if ($binary === false) {
            return null;
        }

        self::deleteStoredImage($oldValue);

        $path = trim($directory, '/').'/'.Str::uuid().'.'.$extension;

        Storage::disk('public')->put($path, $binary);

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }

    public static function deleteStoredImage(?string $value): void
    {
        if (!$value || str_starts_with($value, 'data:image/')) {
            return;
        }

        $path = parse_url($value, PHP_URL_PATH) ?: $value;
        $storagePrefix = '/storage/';

        if (!str_starts_with($path, $storagePrefix)) {
            return;
        }

        Storage::disk('public')->delete(substr($path, strlen($storagePrefix)));
    }
}
