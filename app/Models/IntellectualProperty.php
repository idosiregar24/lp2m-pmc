<?php

namespace App\Models;

use App\Enums\IntellectualPropertyType;
use App\Models\Concerns\HasContributors;
use App\Models\Concerns\HasDocuments;
use App\Models\Concerns\HasVerification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * HKI — Hak Kekayaan Intelektual (PRD §4.8). Owners may include institutions.
 */
class IntellectualProperty extends Model
{
    use HasContributors, HasDocuments, HasFactory, HasVerification, SoftDeletes;

    protected $fillable = ['title', 'year', 'type'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'type' => IntellectualPropertyType::class,
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'verified_by' => 'integer',
        ];
    }

    /**
     * Query-string filters: q, year, faculty, study_program, type, status.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['q'] ?? null)
            ->when($filters['year'] ?? null, fn (Builder $q, $year) => $q->where('year', $year))
            ->filterByUnit($filters['faculty'] ?? null, $filters['study_program'] ?? null)
            ->filterByLecturer($filters['lecturer'] ?? null)
            ->when(IntellectualPropertyType::tryFrom($filters['type'] ?? ''), fn (Builder $q, IntellectualPropertyType $type) => $q->where('type', $type))
            ->status($filters['status'] ?? null);
    }

    /**
     * One-line description for the public lecturer page.
     */
    public function publicSummary(): string
    {
        return $this->type->label();
    }
}
