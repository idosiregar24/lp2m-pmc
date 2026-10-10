<?php

namespace App\Http\Requests\IntellectualProperty;

use App\Http\Requests\Concerns\UpdatesActivity;
use App\Models\IntellectualProperty;

class UpdateIntellectualPropertyRequest extends StoreIntellectualPropertyRequest
{
    use UpdatesActivity;

    protected function record(): IntellectualProperty
    {
        return $this->route('intellectualProperty');
    }
}
