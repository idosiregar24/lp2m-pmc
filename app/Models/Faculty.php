<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  int|null  $include  keep this id even if inactive (current value on edit forms)
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
        return $this->studyPrograms()->exists();
    }
}
