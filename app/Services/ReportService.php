<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Contributor;
use App\Models\User;
use App\Support\Verifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Rekap per dosen (PRD §4.10): how many VERIFIED records of each module list
 * the lecturer as a member (any position). One grouped query per module.
 */
class ReportService
{
    /**
     * Lecturers in the recap — inactive ones too (their past output still counts).
     *
     * @param  array<string, string>  $filters  faculty, study_program, lecturer
     */
    public function lecturers(array $filters): Builder
    {
        return User::query()
            ->where('role', UserRole::Dosen)
            ->with('studyProgram.faculty')
            ->when($filters['lecturer'] ?? null, fn (Builder $q, $id) => $q->whereKey($id))
            ->when($filters['study_program'] ?? null, fn (Builder $q, $id) => $q->where('study_program_id', $id))
            ->when(($filters['faculty'] ?? null) && ! ($filters['study_program'] ?? null),
                fn (Builder $q) => $q->whereHas('studyProgram', fn (Builder $p) => $p->where('faculty_id', $filters['faculty'])))
            ->orderBy('name');
    }

    /**
     * @param  Collection<int, User>|iterable<User>  $lecturers
     * @param  array<string, string>  $filters  year
     * @return Collection<int, array{user: User, counts: array<string, int>, total: int}>
     */
    public function rows(iterable $lecturers, array $filters): Collection
    {
        $lecturers = collect($lecturers);
        $ids = $lecturers->pluck('id');

        $counts = collect(Verifiable::MODULES)->map(fn (array $module, string $alias) => Contributor::query()
            ->where('contributable_type', $alias)
            ->whereIn('user_id', $ids)
            ->whereIn('contributable_id', $module['model']::query()
                ->verified()
                ->when($filters['year'] ?? null, fn (Builder $q, $year) => $q->where('year', $year))
                ->select('id'))
            ->groupBy('user_id')
            ->selectRaw('user_id, count(distinct contributable_id) as total')
            ->pluck('total', 'user_id'));

        return $lecturers->map(function (User $user) use ($counts) {
            $perModule = $counts->map(fn (Collection $totals) => (int) ($totals[$user->id] ?? 0))->all();

            return ['user' => $user, 'counts' => $perModule, 'total' => array_sum($perModule)];
        });
    }
}
