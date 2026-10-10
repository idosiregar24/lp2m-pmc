<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\Concerns\UpdatesActivity;
use App\Models\Book;

class UpdateBookRequest extends StoreBookRequest
{
    use UpdatesActivity;

    protected function record(): Book
    {
        return $this->route('book');
    }
}
