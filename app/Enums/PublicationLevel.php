<?php

namespace App\Enums;

enum PublicationLevel: string
{
    case National = 'NATIONAL';
    case International = 'INTERNATIONAL';

    public function label(): string
    {
        return match ($this) {
            self::National => 'Nasional',
            self::International => 'Internasional',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::National => 'info',
            self::International => 'accent',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
