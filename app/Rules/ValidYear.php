<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Year of a research-data record: min_year (1990) … current year + 1 (PRD §6.3).
 */
class ValidYear implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value) || (int) $value != $value || $value < self::min() || $value > self::max()) {
            $fail('Tahun harus antara '.self::min().' dan '.self::max().'.');
        }
    }

    public static function min(): int
    {
        return (int) config('bp2m.min_year', 1990);
    }

    public static function max(): int
    {
        return (int) now()->year + 1;
    }

    /**
     * Newest first, for <select>.
     *
     * @return array<int, int>
     */
    public static function options(): array
    {
        $years = range(self::max(), self::min());

        return array_combine($years, $years);
    }
}
