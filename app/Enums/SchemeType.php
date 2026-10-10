<?php

namespace App\Enums;

enum SchemeType: string
{
    case Research = 'RESEARCH';
    case CommunityService = 'COMMUNITY_SERVICE';

    public function label(): string
    {
        return match ($this) {
            self::Research => 'Penelitian',
            self::CommunityService => 'Pengabdian',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Research => 'accent',
            self::CommunityService => 'info',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
