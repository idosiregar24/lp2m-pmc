<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\Settings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pengaturan Sistem (PRD §4.12). The logo is the only file on the "public" disk.
 */
class SettingService
{
    private const DISK = 'public';

    public function updateLogo(UploadedFile $file): void
    {
        $old = Settings::get(Settings::LOGO_PATH);

        $path = $file->storeAs('branding', 'logo-'.Str::lower((string) Str::ulid()).'.'.$file->extension(), self::DISK);

        $this->put(Settings::LOGO_PATH, $path);

        if ($old) {
            Storage::disk(self::DISK)->delete($old);
        }
    }

    public function removeLogo(): void
    {
        $old = Settings::get(Settings::LOGO_PATH);

        $this->put(Settings::LOGO_PATH, null);

        if ($old) {
            Storage::disk(self::DISK)->delete($old);
        }
    }

    private function put(string $key, ?string $value): void
    {
        Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Settings::flush();
    }
}
