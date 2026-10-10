<?php

namespace App\Services;

use App\Models\Research;

/**
 * @extends ActivityService<Research>
 */
class ResearchService extends ActivityService
{
    protected function model(): string
    {
        return Research::class;
    }
}
