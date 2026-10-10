<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'ADMIN';
    case Dosen = 'DOSEN';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Dosen => 'Dosen',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Admin => 'accent',
            self::Dosen => 'info',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
