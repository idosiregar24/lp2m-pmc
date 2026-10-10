<?php

namespace App\Models;

use App\Models\Concerns\HasContributors;
use App\Models\Concerns\HasDocuments;
use App\Models\Concerns\HasVerification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Buku (PRD §4.7).
 */
class Book extends Model
{
    use HasContributors, HasDocuments, HasFactory, HasVerification, SoftDeletes;

    protected $fillable = ['title', 'year', 'publisher'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'verified_by' => 'integer',
        ];
    }

    /**
     * Query-string filters: q (also the publisher), year, faculty, study_program, status.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['q'] ?? null, ['publisher'])
            ->when($filters['year'] ?? null, fn (Builder $q, $year) => $q->where('year', $year))
            ->filterByUnit($filters['faculty'] ?? null, $filters['study_program'] ?? null)
            ->filterByLecturer($filters['lecturer'] ?? null)
            ->status($filters['status'] ?? null);
    }

    /**
     * One-line description for the public lecturer page.
     */
    public function publicSummary(): string
    {
        return 'Penerbit: '.$this->publisher;
    }
}
