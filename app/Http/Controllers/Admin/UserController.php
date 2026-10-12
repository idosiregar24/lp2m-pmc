<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use App\Services\LecturerImportService;
use App\Services\UserService;
use App\Support\Verifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Kelola User (PRD §4.2) — Admin only (route role:ADMIN + UserPolicy).
 */
class UserController extends Controller
{
    public function importForm(): View
    {
        $this->authorize('create', User::class);

        return view('users.import');
    }

    public function previewImport(Request $request, LecturerImportService $importService): JsonResponse
    {
        $this->authorize('create', User::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'file.required' => 'Silakan pilih berkas spreadsheet (.xlsx, .xls, .csv).',
            'file.mimes' => 'Format berkas harus berupa spreadsheet Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran berkas tidak boleh melebihi 10 MB.',
        ]);

        try {
            $preview = $importService->preview($request->file('file'));

            return response()->json([
                'success' => true,
                'data' => $preview,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca isi spreadsheet: '.$e->getMessage(),
            ], 422);
        }
    }

    public function processImport(Request $request, LecturerImportService $importService): RedirectResponse
    {
        $this->authorize('create', User::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'update_existing' => ['nullable', 'boolean'],
        ], [
            'file.required' => 'Silakan pilih berkas spreadsheet (.xlsx, .xls, .csv) terlebih dahulu.',
            'file.mimes' => 'Format berkas harus berupa spreadsheet Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran berkas tidak boleh melebihi 10 MB.',
        ]);

        $updateExisting = $request->boolean('update_existing', true);
        $result = $importService->import($request->file('file'), $updateExisting);

        return redirect()->route('users.index')->with(
            'success',
            "Impor data dosen selesai! {$result['imported']} akun dosen baru ditambahkan, {$result['updated']} data dosen diperbarui."
        );
    }

    public function downloadTemplate(LecturerImportService $importService): BinaryFileResponse
    {
        $this->authorize('create', User::class);

        $filePath = $importService->generateTemplate();

        return response()->download($filePath, 'template-data-dosen-pmc.xlsx')->deleteFileAfterSend(true);
    }
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $filters = $this->filters($request, ['q', 'faculty', 'study_program', 'role', 'status']);

        $users = User::query()
            ->with('studyProgram.faculty')
            ->when($filters['q'] ?? null, function (Builder $query, string $term) {
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($term)).'%';
                $query->where(fn (Builder $q) => $q->where('name', 'like', $like)
                    ->orWhere('nidn_nuptk', 'like', $like)
                    ->orWhere('username', 'like', $like));
            })
            ->when($filters['study_program'] ?? null, fn (Builder $q, $id) => $q->where('study_program_id', $id))
            ->when(($filters['faculty'] ?? null) && ! ($filters['study_program'] ?? null),
                fn (Builder $q) => $q->whereHas('studyProgram', fn (Builder $p) => $p->where('faculty_id', $filters['faculty'])))
            ->when(UserRole::tryFrom((string) ($filters['role'] ?? '')), fn (Builder $q, UserRole $role) => $q->where('role', $role))
            ->when(in_array($filters['status'] ?? null, ['active', 'inactive'], true),
                fn (Builder $q) => $q->where('is_active', $filters['status'] === 'active'))
            ->orderBy('name')
            ->paginate(config('bp2m.per_page'))
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'filters' => $filters,
            'faculties' => Faculty::options(),
            'studyPrograms' => StudyProgram::groupedOptions(),
            'roles' => UserRole::options(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'user' => new User(['role' => UserRole::Dosen, 'is_active' => true]),
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreUserRequest $request, UserService $service): RedirectResponse
    {
        $user = $service->create($request->validated());

        return redirect()->route('users.show', $user)
            ->with('success', 'Akun berhasil dibuat. Pengguna wajib mengganti password saat pertama masuk.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load('studyProgram.faculty');

        // Records the user appears on, per module (any status).
        $summary = collect(Verifiable::MODULES)->map(fn (array $module) => [
            'label' => $module['label'],
            'url' => route($module['route'].'.index'),
            'count' => $module['model']::query()
                ->where(fn (Builder $q) => $q->where('created_by', $user->id)
                    ->orWhereHas('contributors', fn (Builder $c) => $c->where('user_id', $user->id)))
                ->count(),
        ]);

        return view('users.show', ['user' => $user, 'summary' => $summary]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $user->load('studyProgram');

        return view('users.edit', [
            'user' => $user,
            ...$this->formOptions($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UserService $service): RedirectResponse
    {
        $service->update($user, $request->validated(), $request->user());

        return redirect()->route('users.show', $user)->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (! request()->user()->can('delete', $user)) {
            return back()->with('error', 'Akun yang sudah memiliki data tidak dapat dihapus — nonaktifkan saja.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }

    public function toggleActive(Request $request, User $user, UserService $service): RedirectResponse
    {
        $this->authorize('toggleActive', $user);

        $service->toggleActive($user, $request->user());

        return back()->with('success', $user->is_active ? 'Akun diaktifkan kembali.' : 'Akun dinonaktifkan.');
    }

    public function changeRole(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manageRole', $user);

        $request->validate([
            'role' => ['required', 'string'],
        ]);

        $newRole = UserRole::tryFrom($request->input('role'));
        if (! $newRole) {
            return back()->with('error', 'Role yang dipilih tidak valid.');
        }

        $user->role = $newRole;
        $user->save();

        $message = match ($newRole) {
            UserRole::Admin => "Hak akses Admin LPPM berhasil diberikan kepada {$user->name}.",
            UserRole::Dosen => "Hak akses {$user->name} berhasil dikembalikan menjadi Dosen.",
            UserRole::SuperAdmin => "Hak akses {$user->name} berhasil diubah menjadi Super Admin.",
        };

        return back()->with('success', $message);
    }

    public function resetPassword(User $user, UserService $service): RedirectResponse
    {
        $this->authorize('resetPassword', $user);

        $password = $service->resetPassword($user);

        return back()
            ->with('success', 'Password berhasil direset. Sampaikan password sementara ini kepada pengguna — hanya ditampilkan sekali.')
            ->with('generated_password', $password);
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?User $user = null): array
    {
        return [
            'roles' => UserRole::options(),
            'faculties' => Faculty::options($user?->studyProgram?->faculty_id),
            'programs' => StudyProgram::jsOptions($user?->study_program_id),
        ];
    }
}
