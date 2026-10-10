<?php

namespace App\Http\Requests\Concerns;

use App\Enums\SchemeType;
use Illuminate\Validation\Rule;

/**
 * scheme_id + funding_source_id of Penelitian / Pengabdian: active masters only,
 * the scheme of the module's own type (PRD §4.3). On update the record's current
 * value stays valid even if it has since been deactivated.
 */
trait ValidatesFunding
{
    protected function schemeRule(SchemeType $type, ?int $current = null): mixed
    {
        return Rule::exists('schemes', 'id')
            ->where('type', $type->value)
            ->where(fn ($q) => $q->where('is_active', true)->when($current, fn ($q) => $q->orWhere('id', $current)));
    }

    protected function fundingSourceRule(?int $current = null): mixed
    {
        return Rule::exists('funding_sources', 'id')
            ->where(fn ($q) => $q->where('is_active', true)->when($current, fn ($q) => $q->orWhere('id', $current)));
    }
}
