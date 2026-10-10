<x-layouts.app title="Ubah Buku" :breadcrumbs="[
    ['label' => 'Buku', 'url' => route('books.index')],
    ['label' => $book->title, 'url' => route('books.show', $book)],
    ['label' => 'Ubah'],
]">
    <x-shared.page-header title="Ubah Buku" :description="$book->title" icon="heroicon-o-book-open" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @include('books._form', ['action' => route('books.update', $book), 'method' => 'PUT'])
        </div>

        {{-- Saved documents live outside the form (each delete is its own form). --}}
        <x-ui.card title="Dokumen Tersimpan" class="self-start">
            <x-shared.documents-list :documents="$book->documents" :deletable="auth()->user()->can('update', $book)" />
        </x-ui.card>
    </div>
</x-layouts.app>
