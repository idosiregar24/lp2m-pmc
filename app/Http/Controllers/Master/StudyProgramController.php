<?php

namespace App\Http\Controllers\Master;

use App\Enums\StudyProgramLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\SaveStudyProgramRequest;
use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyProgramController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request, ['q', 'faculty']);

        return view('master.study-programs.index', [
            'studyPrograms' => StudyProgram::query()
                ->with('faculty')
                ->withCount('users')
                ->when($filters['q'] ?? null, fn ($q, $term) => $q->where('name', 'like', '%'.$term.'%'))
                ->when($filters['faculty'] ?? null, fn ($q, $faculty) => $q->where('faculty_id', $faculty))
                ->orderBy('faculty_id')->orderBy('name')
                ->paginate(config('bp2m.per_page'))
                ->withQueryString(),
            'filters' => $filters,
            'faculties' => Faculty::options(),
        ]);
    }

    public function create(): View
    {
        return view('master.study-programs.form', [
            'studyProgram' => new StudyProgram(['is_active' => true]),
            'faculties' => Faculty::options(),
            'levels' => StudyProgramLevel::options(),
        ]);
    }

    public function store(SaveStudyProgramRequest $request): RedirectResponse
    {
        StudyProgram::create($request->validated());

        return redirect()->route('master.study-programs.index')->with('success', 'Prodi berhasil ditambahkan.');
    }

    public function edit(StudyProgram $studyProgram): View
    {
        return view('master.study-programs.form', [
            'studyProgram' => $studyProgram,
            'faculties' => Faculty::options($studyProgram->faculty_id),
            'levels' => StudyProgramLevel::options(),
        ]);
    }

    public function update(SaveStudyProgramRequest $request, StudyProgram $studyProgram): RedirectResponse
    {
        $studyProgram->update($request->validated());

        return redirect()->route('master.study-programs.index')->with('success', 'Prodi berhasil diperbarui.');
    }

    public function destroy(StudyProgram $studyProgram): RedirectResponse
    {
        if ($studyProgram->isInUse()) {
            return back()->with('error', 'Prodi sudah dipakai pengguna, nonaktifkan saja.');
        }

        $studyProgram->delete();

        return redirect()->route('master.study-programs.index')->with('success', 'Prodi berhasil dihapus.');
    }
}
