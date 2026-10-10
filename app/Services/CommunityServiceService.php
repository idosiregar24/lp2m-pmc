<?php

namespace App\Services;

use App\Models\CommunityService;

/**
 * @extends ActivityService<CommunityService>
 */
class CommunityServiceService extends ActivityService
{
    protected function model(): string
    {
        return CommunityService::class;
    }
}
