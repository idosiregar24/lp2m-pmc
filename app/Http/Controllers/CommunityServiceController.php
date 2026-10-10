<?php

namespace App\Http\Controllers;

use App\Enums\SchemeType;
use App\Enums\VerificationStatus;
use App\Exports\CommunityServiceExport;
use App\Http\Controllers\Concerns\ExportsActivity;
use App\Http\Requests\CommunityService\StoreCommunityServiceRequest;
use App\Http\Requests\CommunityService\UpdateCommunityServiceRequest;
use App\Models\CommunityService;
use App\Models\Faculty;
use App\Models\FundingSource;
use App\Models\Scheme;
use App\Models\StudyProgram;
use App\Models\User;
use App\Rules\ValidYear;
use App\Services\CommunityServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Pengabdian kepada Masyarakat — same shape as ResearchController (the reference module).
 */
class CommunityServiceController extends Controller
{
    use ExportsActivity;

    private const FILTERS = ['q', 'year', 'faculty', 'study_program', 'lecturer', 'scheme', 'funding_source', 'status'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CommunityService::class);

        $filters = $this->filters($request, self::FILTERS);

        return view('community-services.index', [
            'communityServices' => CommunityService::query()
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
            'schemes' => Scheme::options(SchemeType::CommunityService),
            'fundingSources' => FundingSource::options(),
            'statuses' => VerificationStatus::options(),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        return $this->exportActivity($request, CommunityService::class, CommunityServiceExport::class, 'pengabdian');
    }

    public function create(Request $request): View
    {
        $this->authorize('create', CommunityService::class);

        $communityService = new CommunityService(['year' => now()->year]);
        $user = $request->user();

        return view('community-services.create', [
            'communityService' => $communityService,
            // A Dosen starts as the first member of their own record.
            'contributorRows' => $user->isDosen() ? [['user_id' => $user->id]] : [],
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreCommunityServiceRequest $request, CommunityServiceService $service): RedirectResponse
    {
        $communityService = $service->create(
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('community-services.show', $communityService)
            ->with('success', $this->savedMessage($communityService));
    }

    public function show(CommunityService $communityService): View
    {
        $this->authorize('view', $communityService);

        $communityService->load([
            'scheme', 'fundingSource', 'creator', 'updater', 'verifier',
            'contributors.user.studyProgram.faculty',
            'documents',
            'verificationLogs.actor',
        ]);

        return view('community-services.show', ['communityService' => $communityService]);
    }

    public function edit(CommunityService $communityService): View
    {
        $this->authorize('update', $communityService);

        $communityService->load(['contributors', 'documents']);

        return view('community-services.edit', [
            'communityService' => $communityService,
            'contributorRows' => $communityService->contributors->map->only(['user_id', 'name', 'affiliation'])->all(),
            ...$this->formOptions($communityService),
        ]);
    }

    public function update(UpdateCommunityServiceRequest $request, CommunityService $communityService, CommunityServiceService $service): RedirectResponse
    {
        $service->update(
            $communityService,
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('community-services.show', $communityService)
            ->with('success', $this->savedMessage($communityService));
    }

    public function destroy(CommunityService $communityService, CommunityServiceService $service): RedirectResponse
    {
        $this->authorize('delete', $communityService);

        $service->delete($communityService);

        return redirect()->route('community-services.index')->with('success', 'Data pengabdian berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?CommunityService $communityService = null): array
    {
        return [
            'years' => ValidYear::options(),
            'schemes' => Scheme::options(SchemeType::CommunityService, $communityService?->scheme_id),
            'fundingSources' => FundingSource::options($communityService?->funding_source_id),
            'lecturers' => User::lecturerOptions($communityService?->internalContributorIds() ?? []),
        ];
    }

    private function savedMessage(CommunityService $communityService): string
    {
        return match ($communityService->status) {
            VerificationStatus::Submitted => 'Data pengabdian disimpan dan diajukan untuk verifikasi.',
            VerificationStatus::Verified => 'Data pengabdian berhasil disimpan.',
            default => 'Data pengabdian disimpan sebagai draf. Ajukan verifikasi bila sudah lengkap.',
        };
    }
}
