<?php

namespace App\Services;

use App\Models\IntellectualProperty;

/**
 * @extends ActivityService<IntellectualProperty>
 */
class IntellectualPropertyService extends ActivityService
{
    protected function model(): string
    {
        return IntellectualProperty::class;
    }
}
