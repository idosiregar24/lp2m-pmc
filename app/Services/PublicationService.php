<?php

namespace App\Services;

use App\Models\Publication;

/**
 * @extends ActivityService<Publication>
 */
class PublicationService extends ActivityService
{
    protected function model(): string
    {
        return Publication::class;
    }
}
