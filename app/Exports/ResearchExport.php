<?php

namespace App\Exports;

use App\Models\Research;
use Illuminate\Database\Eloquent\Model;

class ResearchExport extends ActivityExport
{
    protected function moduleHeadings(): array
    {
        return ['Jenis Skema', 'Sumber Dana'];
    }

    /**
     * @param  Research  $record
     */
    protected function moduleValues(Model $record): array
    {
        return [$record->scheme->name, $record->fundingSource->name];
    }

    protected function moduleRelations(): array
    {
        return ['scheme', 'fundingSource'];
    }
}
