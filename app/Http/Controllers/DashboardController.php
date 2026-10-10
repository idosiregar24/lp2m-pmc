<?php

namespace App\Http\Controllers;

use App\Enums\VerificationStatus;
use App\Models\Faculty;
use App\Models\User;
use App\Support\Verifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Dashboard (PRD §4.10). Official numbers = VERIFIED only and everything goes
 * through visibleTo(), so a Dosen only ever counts their own records.
 */
class DashboardController extends Controller
{
    private const CHART_YEARS = 5;

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $year = now()->year;
        $chartYears = range($year - self::CHART_YEARS + 1, $year);

        // One grouped count per module; total, this year and the chart all derive from it.
        $modules = collect(Verifiable::MODULES)->map(function (array $module) use ($user, $year, $chartYears) {
            $perYear = $module['model']::query()
                ->visibleTo($user)
                ->verified()
                ->groupBy('year')
                ->selectRaw('year, count(*) as total')
                ->pluck('total', 'year')
                ->map(fn ($total) => (int) $total);

            // Every status (not only VERIFIED) — the workload chart.
            $perStatus = $module['model']::query()
                ->visibleTo($user)
                ->groupBy('status')
                ->selectRaw('status, count(*) as total')
                ->pluck('total', 'status');

            return [
                'label' => $module['label'],
                'icon' => $module['icon'],
                'url' => route($module['route'].'.index'),
                'verified_total' => $perYear->sum(),
                'verified_this_year' => $perYear[$year] ?? 0,
                'chart' => array_map(fn (int $chartYear) => $perYear[$chartYear] ?? 0, $chartYears),
                'statuses' => collect(VerificationStatus::cases())
                    ->mapWithKeys(fn (VerificationStatus $status) => [$status->value => (int) ($perStatus[$status->value] ?? 0)])
                    ->all(),
            ];
        });

        $isAdmin = $user->isAdmin();

        return view('dashboard', [
            'modules' => $modules,
            'year' => $year,
            'chart' => [
                'labels' => array_map('strval', $chartYears),
                'series' => $modules->map(fn (array $module) => ['label' => $module['label'], 'data' => $module['chart']])->values()->all(),
            ],
            'composition' => [
                'labels' => $modules->pluck('label')->values()->all(),
                'data' => $modules->pluck('verified_total')->values()->all(),
            ],
            'statusChart' => [
                'labels' => $modules->pluck('label')->values()->all(),
                'series' => collect(VerificationStatus::cases())->map(fn (VerificationStatus $status) => [
                    'label' => $status->label(),
                    'data' => $modules->map(fn (array $module) => $module['statuses'][$status->value])->values()->all(),
                ])->all(),
                // Draf, Diajukan, Perlu Revisi, Terverifikasi — same hues as the status badges.
                'colors' => ['--color-primary-200', '--color-navy-700', '--color-gold-400', '--color-success'],
            ],
            'pendingCount' => $isAdmin ? Verifiable::pendingCount() : 0,
            'pending' => $isAdmin ? $this->records($user, VerificationStatus::Submitted, 5) : collect(),
            'facultyRecap' => $isAdmin ? $this->facultyRecap() : collect(),
            'needsRevision' => $isAdmin ? collect() : $this->records($user, VerificationStatus::NeedsRevision),
            'drafts' => $isAdmin ? collect() : $this->records($user, VerificationStatus::Draft),
            'latest' => $isAdmin ? collect() : $this->records($user, null, 5),
        ]);
    }

    /**
     * Records across all modules, newest first; null status = any status.
     */
    private function records(User $user, ?VerificationStatus $status, int $limit = 10): Collection
    {
        return collect(Verifiable::MODULES)
            ->flatMap(fn (array $module) => $module['model']::query()
                ->visibleTo($user)
                ->when($status, fn ($q) => $q->status($status))
                ->latest('updated_at')
                ->limit($limit)
                ->get()
                ->map(fn ($record) => [
                    'module' => $module['label'],
                    'title' => $record->title,
                    'year' => $record->year,
                    'status' => $record->status,
                    'note' => $record->revision_note,
                    'url' => route($module['route'].'.show', $record),
                    'updated_at' => $record->updated_at,
                ]))
            ->sortByDesc('updated_at')
            ->take($limit)
            ->values();
    }

    /**
     * Verified records per faculty and module. A record counts for every faculty of
     * its internal members (PRD §6.1) — once per faculty.
     *
     * @return Collection<int, array{name: string, counts: array<string, int>, total: int}>
     */
    private function facultyRecap(): Collection
    {
        $counts = collect(Verifiable::MODULES)->map(fn (array $module, string $alias) => DB::table('contributors')
            ->join('users', 'users.id', '=', 'contributors.user_id')
            ->join('study_programs', 'study_programs.id', '=', 'users.study_program_id')
            ->where('contributors.contributable_type', $alias)
            ->whereIn('contributors.contributable_id', $module['model']::query()->verified()->select('id'))
            ->groupBy('study_programs.faculty_id')
            ->selectRaw('study_programs.faculty_id, count(distinct contributors.contributable_id) as total')
            ->pluck('total', 'faculty_id'));

        return Faculty::query()->orderBy('name')->get(['id', 'name'])
            ->map(function (Faculty $faculty) use ($counts) {
                $perModule = $counts->map(fn (Collection $totals) => (int) ($totals[$faculty->id] ?? 0))->all();

                return ['name' => $faculty->name, 'counts' => $perModule, 'total' => array_sum($perModule)];
            });
    }
}
