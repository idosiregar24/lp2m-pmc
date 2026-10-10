<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Number;

/**
 * Supporting document (private PDF). Written only by DocumentService and
 * served only through DocumentController@download.
 */
class Document extends Model
{
    protected $fillable = ['original_name', 'path', 'mime_type', 'size', 'uploaded_by'];

    protected $hidden = ['path'];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function humanSize(): string
    {
        return Number::fileSize($this->size, precision: 1);
    }
}
