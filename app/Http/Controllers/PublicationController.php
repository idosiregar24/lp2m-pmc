<?php

namespace App\Http\Controllers;

use App\Enums\PublicationLevel;
use App\Enums\VenueType;
use App\Enums\VerificationStatus;
use App\Exports\PublicationExport;
use App\Http\Controllers\Concerns\ExportsActivity;
use App\Http\Requests\Publication\StorePublicationRequest;
use App\Http\Requests\Publication\UpdatePublicationRequest;
use App\Models\Faculty;
use App\Models\Publication;
use App\Models\StudyProgram;
use App\Models\User;
use App\Rules\ValidYear;
use App\Services\PublicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Publikasi — same shape as ResearchController (the reference module).
 */
class PublicationController extends Controller
{
    use ExportsActivity;

    private const FILTERS = ['q', 'year', 'faculty', 'study_program', 'lecturer', 'venue_type', 'level', 'status'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Publication::class);

        $filters = $this->filters($request, self::FILTERS);

        return view('publications.index', [
            'publications' => Publication::query()
                ->visibleTo($request->user())
                ->filter($filters)
                ->with(['leadContributor.user'])
                ->withCount(['documents', 'contributors'])
                ->latest('year')->latest('id')
                ->paginate(config('bp2m.per_page'))
                ->withQueryString(),
            'filters' => $filters,
            'lecturer' => $this->lecturerFilter($request, $filters),
            'years' => ValidYear::options(),
            'faculties' => Faculty::options(),
            'studyPrograms' => StudyProgram::groupedOptions(),
            'venueTypes' => VenueType::options(),
            'levels' => PublicationLevel::options(),
            'statuses' => VerificationStatus::options(),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        return $this->exportActivity($request, Publication::class, PublicationExport::class, 'publikasi');
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Publication::class);

        $publication = new Publication(['year' => now()->year]);
        $user = $request->user();

        return view('publications.create', [
            'publication' => $publication,
            // A Dosen starts as the first member of their own record.
            'contributorRows' => $user->isDosen() ? [['user_id' => $user->id]] : [],
            ...$this->formOptions(),
        ]);
    }

    public function store(StorePublicationRequest $request, PublicationService $service): RedirectResponse
    {
        $publication = $service->create(
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('publications.show', $publication)
            ->with('success', $this->savedMessage($publication));
    }

    public function show(Publication $publication): View
    {
        $this->authorize('view', $publication);

        $publication->load([
            'creator', 'updater', 'verifier',
            'contributors.user.studyProgram.faculty',
            'documents',
            'verificationLogs.actor',
        ]);

        return view('publications.show', ['publication' => $publication]);
    }

    public function edit(Publication $publication): View
    {
        $this->authorize('update', $publication);

        $publication->load(['contributors', 'documents']);

        return view('publications.edit', [
            'publication' => $publication,
            'contributorRows' => $publication->contributors->map->only(['user_id', 'name', 'affiliation'])->all(),
            ...$this->formOptions($publication),
        ]);
    }

    public function update(UpdatePublicationRequest $request, Publication $publication, PublicationService $service): RedirectResponse
    {
        $service->update(
            $publication,
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('publications.show', $publication)
            ->with('success', $this->savedMessage($publication));
    }

    public function destroy(Publication $publication, PublicationService $service): RedirectResponse
    {
        $this->authorize('delete', $publication);

        $service->delete($publication);

        return redirect()->route('publications.index')->with('success', 'Data publikasi berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?Publication $publication = null): array
    {
        return [
            'years' => ValidYear::options(),
            'venueTypes' => VenueType::options(),
            'levels' => PublicationLevel::options(),
            'lecturers' => User::lecturerOptions($publication?->internalContributorIds() ?? []),
        ];
    }

    private function savedMessage(Publication $publication): string
    {
        return match ($publication->status) {
            VerificationStatus::Submitted => 'Data publikasi disimpan dan diajukan untuk verifikasi.',
            VerificationStatus::Verified => 'Data publikasi berhasil disimpan.',
            default => 'Data publikasi disimpan sebagai draf. Ajukan verifikasi bila sudah lengkap.',
        };
    }
}
