<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingSource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<int, string>
     */
    public static function options(?int $include = null): array
    {
        return self::query()
            ->where(fn (Builder $q) => $q->where('is_active', true)->when($include, fn (Builder $q) => $q->orWhere('id', $include)))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function isInUse(): bool
    {
        return Research::withTrashed()->where('funding_source_id', $this->id)->exists()
            || CommunityService::withTrashed()->where('funding_source_id', $this->id)->exists();
    }
}
