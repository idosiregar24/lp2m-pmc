<?php

namespace App\Exports;

use App\Models\Publication;
use Illuminate\Database\Eloquent\Model;

class PublicationExport extends ActivityExport
{
    protected function moduleHeadings(): array
    {
        return ['Jenis Wadah', 'Nama Jurnal/Prosiding', 'Level', 'Penerbit'];
    }

    /**
     * @param  Publication  $record
     */
    protected function moduleValues(Model $record): array
    {
        return [$record->venue_type->label(), $record->venue_name, $record->level->label(), $record->publisher];
    }
}
