<?php

namespace App\Http\Controllers;

use App\Enums\VerificationStatus;
use App\Exports\BookExport;
use App\Http\Controllers\Concerns\ExportsActivity;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use App\Rules\ValidYear;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Buku — same shape as ResearchController (the reference module).
 */
class BookController extends Controller
{
    use ExportsActivity;

    private const FILTERS = ['q', 'year', 'faculty', 'study_program', 'lecturer', 'status'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Book::class);

        $filters = $this->filters($request, self::FILTERS);

        return view('books.index', [
            'books' => Book::query()
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
            'statuses' => VerificationStatus::options(),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        return $this->exportActivity($request, Book::class, BookExport::class, 'buku');
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Book::class);

        $book = new Book(['year' => now()->year]);
        $user = $request->user();

        return view('books.create', [
            'book' => $book,
            // A Dosen starts as the first member of their own record.
            'contributorRows' => $user->isDosen() ? [['user_id' => $user->id]] : [],
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreBookRequest $request, BookService $service): RedirectResponse
    {
        $book = $service->create(
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('books.show', $book)
            ->with('success', $this->savedMessage($book));
    }

    public function show(Book $book): View
    {
        $this->authorize('view', $book);

        $book->load([
            'creator', 'updater', 'verifier',
            'contributors.user.studyProgram.faculty',
            'documents',
            'verificationLogs.actor',
        ]);

        return view('books.show', ['book' => $book]);
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        $book->load(['contributors', 'documents']);

        return view('books.edit', [
            'book' => $book,
            'contributorRows' => $book->contributors->map->only(['user_id', 'name', 'affiliation'])->all(),
            ...$this->formOptions($book),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book, BookService $service): RedirectResponse
    {
        $service->update(
            $book,
            $request->validated(),
            $request->file('documents', []),
            $request->user(),
            submit: $request->boolean('submit'),
        );

        return redirect()->route('books.show', $book)
            ->with('success', $this->savedMessage($book));
    }

    public function destroy(Book $book, BookService $service): RedirectResponse
    {
        $this->authorize('delete', $book);

        $service->delete($book);

        return redirect()->route('books.index')->with('success', 'Data buku berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?Book $book = null): array
    {
        return [
            'years' => ValidYear::options(),
            'lecturers' => User::lecturerOptions($book?->internalContributorIds() ?? []),
        ];
    }

    private function savedMessage(Book $book): string
    {
        return match ($book->status) {
            VerificationStatus::Submitted => 'Data buku disimpan dan diajukan untuk verifikasi.',
            VerificationStatus::Verified => 'Data buku berhasil disimpan.',
            default => 'Data buku disimpan sebagai draf. Ajukan verifikasi bila sudah lengkap.',
        };
    }
}
