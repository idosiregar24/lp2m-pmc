<?php

namespace App\Http\Requests\Publication;

use App\Http\Requests\Concerns\UpdatesActivity;
use App\Models\Publication;

class UpdatePublicationRequest extends StorePublicationRequest
{
    use UpdatesActivity;

    protected function record(): Publication
    {
        return $this->route('publication');
    }
}
