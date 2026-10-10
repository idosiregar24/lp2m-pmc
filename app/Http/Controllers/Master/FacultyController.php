<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\SaveFacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request, ['q']);

        return view('master.faculties.index', [
            'faculties' => Faculty::query()
                ->withCount('studyPrograms')
                ->when($filters['q'] ?? null, fn ($q, $term) => $q->where('name', 'like', '%'.$term.'%'))
                ->orderBy('name')
                ->paginate(config('bp2m.per_page'))
                ->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('master.faculties.form', ['faculty' => new Faculty(['is_active' => true])]);
    }

    public function store(SaveFacultyRequest $request): RedirectResponse
    {
        Faculty::create($request->validated());

        return redirect()->route('master.faculties.index')->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function edit(Faculty $faculty): View
    {
        return view('master.faculties.form', ['faculty' => $faculty]);
    }

    public function update(SaveFacultyRequest $request, Faculty $faculty): RedirectResponse
    {
        $faculty->update($request->validated());

        return redirect()->route('master.faculties.index')->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        if ($faculty->isInUse()) {
            return back()->with('error', 'Fakultas sudah dipakai prodi, nonaktifkan saja.');
        }

        $faculty->delete();

        return redirect()->route('master.faculties.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}
