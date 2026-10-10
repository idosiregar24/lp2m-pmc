<?php

namespace App\Enums;

enum StudyProgramLevel: string
{
    case D3 = 'D3';
    case D4 = 'D4';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';
    case Profesi = 'PROFESI';

    public function label(): string
    {
        return match ($this) {
            self::Profesi => 'Profesi',
            default => $this->value,
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
