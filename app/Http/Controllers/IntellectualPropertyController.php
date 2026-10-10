<?php

namespace App\Http\Controllers;

use App\Enums\IntellectualPropertyType;
use App\Enums\VerificationStatus;
use App\Exports\IntellectualPropertyExport;
use App\Http\Controllers\Concerns\ExportsActivity;
use App\Http\Requests\IntellectualProperty\StoreIntellectualPropertyRequest;
use App\Http\Requests\IntellectualProperty\UpdateIntellectualPropertyRequest;
use App\Models\Faculty;
use App\Models\IntellectualProperty;
use App\Models\StudyProgram;
use App\Models\User;
use App\Rules\ValidYear;
use App\Services\IntellectualPropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * HKI — same shape as ResearchController (the reference module).
 */
class IntellectualPropertyController extends Controller
{
    use ExportsActivity;

    private const FILTERS = ['q', 'year', 'faculty', 'study_program', 'lecturer', 'type', 'status'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', IntellectualProperty::class);

        $filters = $this->filters($request, self::FILTERS);

        return view('intellectual-properties.index', [
            'intellectualProperties' => IntellectualProperty::query()
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
            'types' => IntellectualPropertyType::options(),
            'statuses' => VerificationStatus::options(),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        return $this->exportActivity($request, IntellectualProperty::class, IntellectualPropertyExport::class, 'hki');
    }

    public function create(Request $request): View
    {
        $this->authorize('create', IntellectualProperty::class);

        $intellectualProperty = new IntellectualProperty(['year' => now()->year]);
        $user = $request->user();

        return view('intellectual-properties.create', [
            'intellectualProperty' => $intellectualProperty,
            // A Dosen starts as the first member of their own record.
            'contributorRows' => $user->isDosen() ? [['user_id' => $user->id]] : [],
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreIntellectualPropertyRequest $request, IntellectualPropertyService $service): RedirectResponse
    {
        $intellectualProperty = $service->create(
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('intellectual-properties.show', $intellectualProperty)
            ->with('success', $this->savedMessage($intellectualProperty));
    }

    public function show(IntellectualProperty $intellectualProperty): View
    {
        $this->authorize('view', $intellectualProperty);

        $intellectualProperty->load([
            'creator', 'updater', 'verifier',
            'contributors.user.studyProgram.faculty',
            'documents',
            'verificationLogs.actor',
        ]);

        return view('intellectual-properties.show', ['intellectualProperty' => $intellectualProperty]);
    }

    public function edit(IntellectualProperty $intellectualProperty): View
    {
        $this->authorize('update', $intellectualProperty);

        $intellectualProperty->load(['contributors', 'documents']);

        return view('intellectual-properties.edit', [
            'intellectualProperty' => $intellectualProperty,
            'contributorRows' => $intellectualProperty->contributors->map->only(['user_id', 'name', 'affiliation'])->all(),
            ...$this->formOptions($intellectualProperty),
        ]);
    }

    public function update(UpdateIntellectualPropertyRequest $request, IntellectualProperty $intellectualProperty, IntellectualPropertyService $service): RedirectResponse
    {
        $service->update(
            $intellectualProperty,
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('intellectual-properties.show', $intellectualProperty)
            ->with('success', $this->savedMessage($intellectualProperty));
    }

    public function destroy(IntellectualProperty $intellectualProperty, IntellectualPropertyService $service): RedirectResponse
    {
        $this->authorize('delete', $intellectualProperty);

        $service->delete($intellectualProperty);

        return redirect()->route('intellectual-properties.index')->with('success', 'Data HKI berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?IntellectualProperty $intellectualProperty = null): array
    {
        return [
            'years' => ValidYear::options(),
            'types' => IntellectualPropertyType::options(),
            'lecturers' => User::lecturerOptions($intellectualProperty?->internalContributorIds() ?? []),
        ];
    }

    private function savedMessage(IntellectualProperty $intellectualProperty): string
    {
        return match ($intellectualProperty->status) {
            VerificationStatus::Submitted => 'Data HKI disimpan dan diajukan untuk verifikasi.',
            VerificationStatus::Verified => 'Data HKI berhasil disimpan.',
            default => 'Data HKI disimpan sebagai draf. Ajukan verifikasi bila sudah lengkap.',
        };
    }
}
