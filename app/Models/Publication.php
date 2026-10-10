<?php

namespace App\Models;

use App\Enums\PublicationLevel;
use App\Enums\VenueType;
use App\Models\Concerns\HasContributors;
use App\Models\Concerns\HasDocuments;
use App\Models\Concerns\HasVerification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Publikasi — journal article or proceeding paper (PRD §4.6).
 */
class Publication extends Model
{
    use HasContributors, HasDocuments, HasFactory, HasVerification, SoftDeletes;

    protected $fillable = ['title', 'year', 'venue_type', 'venue_name', 'level', 'publisher'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'venue_type' => VenueType::class,
            'level' => PublicationLevel::class,
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'verified_by' => 'integer',
        ];
    }

    /**
     * Query-string filters: q (also venue name / publisher), year, faculty,
     * study_program, venue_type, level, status.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['q'] ?? null, ['venue_name', 'publisher'])
            ->when($filters['year'] ?? null, fn (Builder $q, $year) => $q->where('year', $year))
            ->filterByUnit($filters['faculty'] ?? null, $filters['study_program'] ?? null)
            ->filterByLecturer($filters['lecturer'] ?? null)
            ->when(VenueType::tryFrom($filters['venue_type'] ?? ''), fn (Builder $q, VenueType $type) => $q->where('venue_type', $type))
            ->when(PublicationLevel::tryFrom($filters['level'] ?? ''), fn (Builder $q, PublicationLevel $level) => $q->where('level', $level))
            ->status($filters['status'] ?? null);
    }

    /**
     * One-line description for the public lecturer page.
     */
    public function publicSummary(): string
    {
        return $this->venue_name.' · '.$this->venue_type->label().' '.$this->level->label().' · '.$this->publisher;
    }
}
