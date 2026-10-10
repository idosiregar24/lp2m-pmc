<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerificationNoteRequest;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Rules\ValidYear;
use App\Services\VerificationService;
use App\Support\Verifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/**
 * Admin queue of SUBMITTED records across all modules + the decisions (PRD §4.11).
 */
class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $type = is_string($type) && Verifiable::exists($type) ? $type : null;
        $filters = $this->filters($request, ['q', 'year', 'faculty', 'study_program']);

        $queries = collect(Verifiable::MODULES)->map(fn (array $module) => $module['model']::query()
            ->status(VerificationStatus::Submitted)
            ->search($filters['q'] ?? null)
            ->when($filters['year'] ?? null, fn ($q, $year) => $q->where('year', $year))
            ->filterByUnit($filters['faculty'] ?? null, $filters['study_program'] ?? null));

        $counts = $queries->map(fn ($query) => (clone $query)->count());

        $records = $queries
            ->when($type, fn ($all) => $all->only($type))
            ->flatMap(fn ($query) => $query->with(['leadContributor.user', 'creator'])->withCount('documents')->get())
            ->sortBy(fn (Model $record) => $record->submitted_at)
            ->values();

        $perPage = (int) config('bp2m.per_page');
        $page = LengthAwarePaginator::resolveCurrentPage();

        return view('verifications.index', [
            'records' => new LengthAwarePaginator(
                $records->forPage($page, $perPage)->values(),
                $records->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()],
            ),
            'type' => $type,
            'counts' => $counts,
            'filters' => $filters,
            'years' => ValidYear::options(),
            'faculties' => Faculty::options(),
            'studyPrograms' => StudyProgram::groupedOptions(),
        ]);
    }

    public function approve(Request $request, string $type, int $id, VerificationService $verification): RedirectResponse
    {
        $record = Verifiable::resolve($type, $id);
        $this->authorize('approve', $record);

        $verification->verify($record, $request->user());

        return back()->with('success', 'Data berhasil diverifikasi.');
    }

    public function requestRevision(VerificationNoteRequest $request, string $type, int $id, VerificationService $verification): RedirectResponse
    {
        $record = Verifiable::resolve($type, $id);
        $this->authorize('requestRevision', $record);

        $verification->requestRevision($record, $request->user(), $request->validated('note'));

        return back()->with('success', 'Data dikembalikan ke dosen untuk direvisi.');
    }

    public function reopen(VerificationNoteRequest $request, string $type, int $id, VerificationService $verification): RedirectResponse
    {
        $record = Verifiable::resolve($type, $id);
        $this->authorize('reopen', $record);

        $verification->reopen($record, $request->user(), $request->validated('note'));

        return back()->with('success', 'Verifikasi dibuka kembali; data dikembalikan ke dosen untuk direvisi.');
    }
}
