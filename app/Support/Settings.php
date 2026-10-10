<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Read access to the key-value `settings` table (cached forever, flushed on write).
 */
final class Settings
{
    public const CACHE_KEY = 'bp2m.settings';

    public const LOGO_PATH = 'logo_path';

    /** @return array<string, ?string> */
    public static function all(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn () => Setting::query()->pluck('value', 'key')->all());
        } catch (QueryException) {
            return []; // table not migrated yet
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::all()[$key] ?? $default;
    }

    public static function logoUrl(): ?string
    {
        $path = self::get(self::LOGO_PATH);

        return $path ? Storage::disk('public')->url($path) : null;
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
