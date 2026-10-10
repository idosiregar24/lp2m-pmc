<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;

class BookExport extends ActivityExport
{
    protected function moduleHeadings(): array
    {
        return ['Penerbit'];
    }

    /**
     * @param  Book  $record
     */
    protected function moduleValues(Model $record): array
    {
        return [$record->publisher];
    }
}
