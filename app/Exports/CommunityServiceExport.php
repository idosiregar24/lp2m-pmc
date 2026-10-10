<?php

namespace App\Exports;

use App\Models\CommunityService;
use Illuminate\Database\Eloquent\Model;

class CommunityServiceExport extends ActivityExport
{
    protected function moduleHeadings(): array
    {
        return ['Jenis Skema', 'Mitra', 'Sumber Dana'];
    }

    /**
     * @param  CommunityService  $record
     */
    protected function moduleValues(Model $record): array
    {
        return [$record->scheme->name, $record->partner, $record->fundingSource->name];
    }

    protected function moduleRelations(): array
    {
        return ['scheme', 'fundingSource'];
    }
}
