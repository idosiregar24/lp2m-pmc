<?php

namespace App\Models;

use App\Models\Concerns\HasContributors;
use App\Models\Concerns\HasDocuments;
use App\Models\Concerns\HasVerification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Penelitian (PRD §4.4) — the reference module the other data modules copy.
 */
class Research extends Model
{
    use HasContributors, HasDocuments, HasFactory, HasVerification, SoftDeletes;

    // Laravel treats "research" as uncountable — the table is explicitly plural.
    protected $table = 'researches';

    protected $fillable = ['title', 'year', 'scheme_id', 'funding_source_id'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'verified_by' => 'integer',
        ];
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function fundingSource(): BelongsTo
    {
        return $this->belongsTo(FundingSource::class);
    }

    /**
     * Query-string filters of the list page: q, year, faculty, study_program,
     * scheme, funding_source, status.
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
            ->when($filters['scheme'] ?? null, fn (Builder $q, $scheme) => $q->where('scheme_id', $scheme))
            ->when($filters['funding_source'] ?? null, fn (Builder $q, $source) => $q->where('funding_source_id', $source))
            ->status($filters['status'] ?? null);
    }

    /**
     * One-line description for the public lecturer page (relations: scheme, fundingSource).
     */
    public function publicSummary(): string
    {
        return $this->scheme->name.' · '.$this->fundingSource->name;
    }
}
