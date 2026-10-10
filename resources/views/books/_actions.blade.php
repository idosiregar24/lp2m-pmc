{{-- Row actions, shared by the table and the phone cards. --}}
<div class="inline-flex items-center gap-1">
    <a href="{{ route('books.show', $book) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Lihat {{ $book->title }}">
        <x-heroicon-o-eye class="size-4" />
    </a>
    @can('update', $book)
        <a href="{{ route('books.edit', $book) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Ubah {{ $book->title }}">
            <x-heroicon-o-pencil-square class="size-4" />
        </a>
    @endcan
    @can('delete', $book)
        <x-shared.delete-button :action="route('books.destroy', $book)" :name="$book->title" title="Hapus buku?" icon-only />
    @endcan
</div>
