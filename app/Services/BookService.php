<?php

namespace App\Services;

use App\Models\Book;

/**
 * @extends ActivityService<Book>
 */
class BookService extends ActivityService
{
    protected function model(): string
    {
        return Book::class;
    }
}
