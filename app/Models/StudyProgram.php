<?php

namespace App\Models;

use App\Enums\StudyProgramLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyProgram extends Model
{
    use HasFactory;

    protected $fillable = ['faculty_id', 'name', 'level', 'is_active'];

    protected function casts(): array
    {
        return [
            'level' => StudyProgramLevel::class,
            'is_active' => 'boolean',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function displayName(): string
    {
        return "{$this->level->label()} {$this->name}";
    }

    /**
     * Grouped by faculty for <select>: ['Fakultas X' => [id => 'S1 Keperawatan']].
     *
     * @return array<string, array<int, string>>
     */
    public static function groupedOptions(?int $include = null): array
    {
        return self::query()
            ->with('faculty:id,name')
            ->where(fn (Builder $q) => $q->where('is_active', true)->when($include, fn (Builder $q) => $q->orWhere('id', $include)))
            ->orderBy('name')
            ->get()
            ->sortBy(fn (self $program) => $program->faculty->name)
            ->groupBy(fn (self $program) => $program->faculty->name)
            ->map(fn ($programs) => $programs->mapWithKeys(fn (self $program) => [$program->id => $program->displayName()])->all())
            ->all();
    }

    /**
     * Flat list for the Alpine faculty → prodi filter.
     *
     * @return list<array{id: string, label: string, faculty_id: string}>
     */
    public static function jsOptions(?int $include = null): array
    {
        return self::query()
            ->where(fn (Builder $q) => $q->where('is_active', true)->when($include, fn (Builder $q) => $q->orWhere('id', $include)))
            ->orderBy('name')
            ->get()
            ->map(fn (self $program) => [
                'id' => (string) $program->id,
                'label' => $program->displayName(),
                'faculty_id' => (string) $program->faculty_id,
            ])
            ->all();
    }

    public function isInUse(): bool
    {
        return $this->users()->exists();
    }
}
