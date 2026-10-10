<?php

namespace App\Http\Controllers;

use App\Enums\SchemeType;
use App\Enums\VerificationStatus;
use App\Exports\ResearchExport;
use App\Http\Controllers\Concerns\ExportsActivity;
use App\Http\Requests\Research\StoreResearchRequest;
use App\Http\Requests\Research\UpdateResearchRequest;
use App\Models\Faculty;
use App\Models\FundingSource;
use App\Models\Research;
use App\Models\Scheme;
use App\Models\StudyProgram;
use App\Models\User;
use App\Rules\ValidYear;
use App\Services\ResearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResearchController extends Controller
{
    use ExportsActivity;

    private const FILTERS = ['q', 'year', 'faculty', 'study_program', 'lecturer', 'scheme', 'funding_source', 'status'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Research::class);

        $filters = $this->filters($request, self::FILTERS);

        return view('researches.index', [
            'researches' => Research::query()
                ->visibleTo($request->user())
                ->filter($filters)
                ->with(['leadContributor.user', 'scheme', 'fundingSource'])
                ->withCount(['documents', 'contributors'])
                ->latest('year')->latest('id')
                ->paginate(config('bp2m.per_page'))
                ->withQueryString(),
            'filters' => $filters,
            'lecturer' => $this->lecturerFilter($request, $filters),
            'years' => ValidYear::options(),
            'faculties' => Faculty::options(),
            'studyPrograms' => StudyProgram::groupedOptions(),
            'schemes' => Scheme::options(SchemeType::Research),
            'fundingSources' => FundingSource::options(),
            'statuses' => VerificationStatus::options(),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        return $this->exportActivity($request, Research::class, ResearchExport::class, 'penelitian');
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Research::class);

        $research = new Research(['year' => now()->year]);
        $user = $request->user();

        return view('researches.create', [
            'research' => $research,
            // A Dosen starts as Ketua of their own record.
            'contributorRows' => $user->isDosen() ? [['user_id' => $user->id]] : [],
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreResearchRequest $request, ResearchService $service): RedirectResponse
    {
        $research = $service->create(
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('researches.show', $research)
            ->with('success', $this->savedMessage($research));
    }

    public function show(Research $research): View
    {
        $this->authorize('view', $research);

        $research->load([
            'scheme', 'fundingSource', 'creator', 'updater', 'verifier',
            'contributors.user.studyProgram.faculty',
            'documents',
            'verificationLogs.actor',
        ]);

        return view('researches.show', ['research' => $research]);
    }

    public function edit(Research $research): View
    {
        $this->authorize('update', $research);

        $research->load(['contributors', 'documents']);

        return view('researches.edit', [
            'research' => $research,
            'contributorRows' => $research->contributors->map->only(['user_id', 'name', 'affiliation'])->all(),
            ...$this->formOptions($research),
        ]);
    }

    public function update(UpdateResearchRequest $request, Research $research, ResearchService $service): RedirectResponse
    {
        $service->update(
            $research,
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('researches.show', $research)
            ->with('success', $this->savedMessage($research));
    }

    public function destroy(Research $research, ResearchService $service): RedirectResponse
    {
        $this->authorize('delete', $research);

        $service->delete($research);

        return redirect()->route('researches.index')->with('success', 'Data penelitian berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?Research $research = null): array
    {
        return [
            'years' => ValidYear::options(),
            'schemes' => Scheme::options(SchemeType::Research, $research?->scheme_id),
            'fundingSources' => FundingSource::options($research?->funding_source_id),
            'lecturers' => User::lecturerOptions($research?->internalContributorIds() ?? []),
        ];
    }

    private function savedMessage(Research $research): string
    {
        return match ($research->status) {
            VerificationStatus::Submitted => 'Data penelitian disimpan dan diajukan untuk verifikasi.',
            VerificationStatus::Verified => 'Data penelitian berhasil disimpan.',
            default => 'Data penelitian disimpan sebagai draf. Ajukan verifikasi bila sudah lengkap.',
        };
    }
}
