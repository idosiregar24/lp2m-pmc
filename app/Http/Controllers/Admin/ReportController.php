<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use App\Rules\ValidYear;
use App\Services\ReportService;
use App\Support\Verifiable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Rekap per dosen — Admin only (route role:ADMIN). Counts are VERIFIED records.
 */
class ReportController extends Controller
{
    private const FILTERS = ['year', 'faculty', 'study_program', 'lecturer'];

    public function index(Request $request, ReportService $reports): View
    {
        $filters = $this->filters($request, self::FILTERS);

        $lecturers = $reports->lecturers($filters)
            ->paginate(config('bp2m.per_page'))
            ->withQueryString();

        return view('reports.index', [
            'lecturers' => $lecturers,
            'rows' => $reports->rows($lecturers->items(), $filters),
            'modules' => Verifiable::MODULES,
            'filters' => $filters,
            'years' => ValidYear::options(),
            'faculties' => Faculty::options(),
            'studyPrograms' => StudyProgram::groupedOptions(),
            'lecturerOptions' => User::query()->where('role', UserRole::Dosen)->orderBy('name')->pluck('name', 'id')->all(),
        ]);
    }

    public function export(Request $request, ReportService $reports): BinaryFileResponse
    {
        $filters = $this->filters($request, self::FILTERS);

        return Excel::download(
            new ReportExport($reports->rows($reports->lecturers($filters)->get(), $filters), $filters['year'] ?? null),
            'rekap-dosen-'.now()->format('Ymd-His').'.xlsx',
        );
    }
}
