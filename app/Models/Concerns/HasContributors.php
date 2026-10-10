<?php

namespace App\Models\Concerns;

use App\Models\Contributor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * People on a research-data record + the ownership rule (PRD §7). This trait
 * is the ONLY place "whose data is this" is decided for lists; ActivityPolicy
 * reuses isVisibleTo() / isLedOrCreatedBy() for single records.
 */
trait HasContributors
{
    public function contributors(): MorphMany
    {
        return $this->morphMany(Contributor::class, 'contributable')->orderBy('sort_order');
    }

    public function leadContributor(): MorphOne
    {
        return $this->morphOne(Contributor::class, 'contributable')->where('sort_order', 1);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Admin sees everything; a Dosen sees records they created or are an internal contributor on.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(fn (Builder $q) => $q
            ->where($this->qualifyColumn('created_by'), $user->id)
            ->orWhereHas('contributors', fn (Builder $c) => $c->where('user_id', $user->id)));
    }

    /**
     * Title, one of the module's extra text columns, or a contributor name contains the term.
     *
     * @param  list<string>  $columns  extra columns of the module (partner, publisher, …)
     */
    public function scopeSearch(Builder $query, ?string $term, array $columns = []): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $q) use ($like, $columns) {
            $q->where($this->qualifyColumn('title'), 'like', $like);

            foreach ($columns as $column) {
                $q->orWhere($this->qualifyColumn($column), 'like', $like);
            }

            $q->orWhereHas('contributors', fn (Builder $c) => $c
                ->where('name', 'like', $like)
                ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', $like)->orWhere('nidn_nuptk', 'like', $like)));
        });
    }

    /**
     * Records that list the lecturer as a member (any position) — the Rekap drill-down.
     */
    public function scopeFilterByLecturer(Builder $query, mixed $lecturer = null): Builder
    {
        return $query->when($lecturer, fn (Builder $q) => $q
            ->whereHas('contributors', fn (Builder $c) => $c->where('user_id', $lecturer)));
    }

    /**
     * Faculty / prodi of a record = those of its internal contributors (PRD §6.1).
     */
    public function scopeFilterByUnit(Builder $query, mixed $faculty = null, mixed $studyProgram = null): Builder
    {
        return $query
            ->when($studyProgram, fn (Builder $q) => $q->whereHas('contributors.user', fn (Builder $u) => $u->where('study_program_id', $studyProgram)))
            ->when($faculty && ! $studyProgram, fn (Builder $q) => $q->whereHas('contributors.user.studyProgram', fn (Builder $p) => $p->where('faculty_id', $faculty)));
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->isAdmin()
            || $this->created_by === $user->id
            || $this->contributors()->where('user_id', $user->id)->exists();
    }

    /**
     * Creator or lead contributor (ketua / penulis pertama / pemilik pertama) — [ASUMSI PRD §7].
     */
    public function isLedOrCreatedBy(User $user): bool
    {
        return $this->created_by === $user->id
            || $this->contributors()->where('sort_order', 1)->where('user_id', $user->id)->exists();
    }

    /**
     * @return array<int>
     */
    public function internalContributorIds(): array
    {
        return $this->contributors()->whereNotNull('user_id')->pluck('user_id')->all();
    }
}
