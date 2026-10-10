<?php

namespace App\Enums;

/**
 * Where a publication appeared [ASUMSI PRD §4.6]; drives the "Nama jurnal / prosiding" label.
 */
enum VenueType: string
{
    case Journal = 'JOURNAL';
    case Proceeding = 'PROCEEDING';

    public function label(): string
    {
        return match ($this) {
            self::Journal => 'Jurnal',
            self::Proceeding => 'Prosiding',
        };
    }

    public function venueLabel(): string
    {
        return match ($this) {
            self::Journal => 'Nama Jurnal',
            self::Proceeding => 'Nama Prosiding',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
