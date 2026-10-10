<?php

namespace App\Models;

use App\Enums\SchemeType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'is_active'];

    protected function casts(): array
    {
        return [
            'type' => SchemeType::class,
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<int, string>
     */
    public static function options(SchemeType $type, ?int $include = null): array
    {
        return self::query()
            ->where('type', $type)
            ->where(fn (Builder $q) => $q->where('is_active', true)->when($include, fn (Builder $q) => $q->orWhere('id', $include)))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function isInUse(): bool
    {
        return Research::withTrashed()->where('scheme_id', $this->id)->exists()
            || CommunityService::withTrashed()->where('scheme_id', $this->id)->exists();
    }
}
