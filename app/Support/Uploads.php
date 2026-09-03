<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Uploads
{
    protected static function isRemote(): bool
    {
        return config('filesystems.default') === 'b2';
    }

    protected static function disk()
    {
        return Storage::disk(static::isRemote() ? 'b2' : 'uploads');
    }

    public static function store(string $folder, UploadedFile $file, string $name): bool
    {
        return static::disk()->putFileAs($folder . '/', $file, $name, 'public') !== false;
    }

    public static function put(string $folder, string $name, string $contents): bool
    {
        return static::disk()->put($folder . '/' . $name, $contents, 'public');
    }

    public static function exists(string $folder, string $name): bool
    {
        return static::disk()->exists($folder . '/' . $name);
    }

    public static function delete(string $folder, string $name): void
    {
        if ($name && static::exists($folder, $name)) {
            static::disk()->delete($folder . '/' . $name);
        }
    }

    public static function resolve(string $folder, string $name): ?string
    {
        if (!$name) {
            return null;
        }

        $path = $folder . '/' . $name;

        if (!static::isRemote()) {
            return static::disk()->exists($path) ? static::disk()->path($path) : null;
        }

        if (!static::disk()->exists($path)) {
            return null;
        }

        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ecomm_' . md5($path);
        $modified = (int) static::disk()->lastModified($path);

        if (!file_exists($tmp) || (int) @filemtime($tmp) < $modified) {
            file_put_contents($tmp, static::disk()->get($path));
            @touch($tmp, $modified);
        }

        return $tmp;
    }
}