<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * One person on a research-data record. Written only by ContributorService.
 */
class Contributor extends Model
{
    protected $fillable = ['user_id', 'name', 'affiliation', 'sort_order'];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function contributable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExternal(): bool
    {
        return $this->user_id === null;
    }

    public function isLead(): bool
    {
        return $this->sort_order === 1;
    }

    public function displayName(): string
    {
        return $this->user?->name ?? (string) $this->name;
    }
}
