<?php

namespace App\Exports;

use App\Models\IntellectualProperty;
use Illuminate\Database\Eloquent\Model;

class IntellectualPropertyExport extends ActivityExport
{
    protected function moduleHeadings(): array
    {
        return ['Jenis KI'];
    }

    /**
     * @param  IntellectualProperty  $record
     */
    protected function moduleValues(Model $record): array
    {
        return [$record->type->label()];
    }
}
