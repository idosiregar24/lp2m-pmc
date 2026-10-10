<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use App\Rules\ValidYear;
use App\Services\ReportService;
use App\Support\Verifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public portal (no login): find a lecturer and see their VERIFIED output.
 * Only active DOSEN accounts; never NIDN/NUPTK, documents, drafts or audit data.
 */
class PublicLecturerController extends Controller
{
    private const PER_PAGE = 12;

    public function index(Request $request, ReportService $reports): View
    {
        $filters = $this->filters($request, ['q', 'faculty', 'study_program', 'has_modul', 'sort']);

        $lecturers = $this->lecturers()
            ->with('studyProgram.faculty')
            ->when($filters['q'] ?? null, fn (Builder $q, string $term) => $q
                ->where('name', 'like', '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term).'%'))
            ->when($filters['study_program'] ?? null, fn (Builder $q, $id) => $q->where('study_program_id', $id))
            ->when(($filters['faculty'] ?? null) && ! ($filters['study_program'] ?? null),
                fn (Builder $q) => $q->whereHas('studyProgram', fn (Builder $p) => $p->where('faculty_id', $filters['faculty'])))
            ->when($filters['has_modul'] ?? null, function (Builder $q, string $modul) {
                if (Verifiable::exists($modul)) {
                    $model = Verifiable::MODULES[$modul]['model'];
                    $q->whereHas('contributions', fn (Builder $c) => $c
                        ->where('contributable_type', $modul)
                        ->whereIn('contributable_id', $model::query()->verified()->select('id')));
                }
            })
            ->when(($filters['sort'] ?? null) === 'name_desc', fn (Builder $q) => $q->orderByDesc('name'), fn (Builder $q) => $q->orderBy('name'))
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $stats = collect(Verifiable::MODULES)->map(fn (array $m) => $m['model']::query()->verified()->count());
        $totalLecturers = $this->lecturers()->count();

        return view('public.lecturers.index', [
            'lecturers' => $lecturers,
            'rows' => $reports->rows($lecturers->items(), [])->keyBy(fn (array $row) => $row['user']->id),
            'modules' => Verifiable::MODULES,
            'filters' => $filters,
            'faculties' => Faculty::options(),
            'studyPrograms' => StudyProgram::groupedOptions(),
            'stats' => $stats,
            'totalLecturers' => $totalLecturers,
            'totalOutputs' => $stats->sum(),
        ]);
    }

    public function show(Request $request, int $lecturer, ReportService $reports): View
    {
        $lecturer = $this->lecturers()->with('studyProgram.faculty')->findOrFail($lecturer);
        $filters = $this->filters($request, ['modul', 'year', 'q', 'role', 'sort']);

        $counts = $reports->rows([$lecturer], [])->first()['counts'];
        $type = isset($filters['modul']) && Verifiable::exists($filters['modul'])
            ? $filters['modul']
            : (collect($counts)->filter()->keys()->first() ?? array_key_first(Verifiable::MODULES));
        $module = Verifiable::MODULES[$type];

        $records = $module['model']::query()
            ->verified()
            ->filterByLecturer($lecturer->id)
            ->when($filters['year'] ?? null, fn (Builder $q, $year) => $q->where('year', $year))
            ->when($filters['q'] ?? null, fn (Builder $q, string $term) => $q
                ->where('title', 'like', '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term).'%'))
            ->when($filters['role'] ?? null, function (Builder $q, string $role) use ($lecturer) {
                if ($role === 'lead') {
                    $q->whereHas('contributors', fn (Builder $c) => $c->where('user_id', $lecturer->id)->where('sort_order', 1));
                } elseif ($role === 'member') {
                    $q->whereHas('contributors', fn (Builder $c) => $c->where('user_id', $lecturer->id)->where('sort_order', '>', 1));
                }
            })
            ->when(($filters['sort'] ?? null) === 'oldest', fn (Builder $q) => $q->orderBy('year')->orderBy('id'))
            ->when(($filters['sort'] ?? null) === 'title', fn (Builder $q) => $q->orderBy('title'))
            ->when(! in_array($filters['sort'] ?? null, ['oldest', 'title']), fn (Builder $q) => $q->latest('year')->latest('id'))
            ->with([...$module['public'], 'contributors.user'])
            ->paginate(10)
            ->withQueryString();

        return view('public.lecturers.show', [
            'lecturer' => $lecturer,
            'counts' => $counts,
            'totalCounts' => array_sum($counts),
            'type' => $type,
            'module' => $module,
            'modules' => Verifiable::MODULES,
            'records' => $records,
            'filters' => $filters,
            'years' => ValidYear::options(),
        ]);
    }

    /**
     * Lecturers shown to the public: active DOSEN accounts only.
     */
    private function lecturers(): Builder
    {
        return User::query()->where('role', UserRole::Dosen)->where('is_active', true);
    }
}
