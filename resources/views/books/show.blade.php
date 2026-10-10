<x-layouts.app :title="$book->title" :breadcrumbs="[['label' => 'Buku', 'url' => route('books.index')], ['label' => $book->title]]">
    <x-shared.page-header :title="$book->title" icon="heroicon-o-book-open">
        <x-slot:meta>
            <span class="tabular-nums">{{ $book->year }}</span>
            <span aria-hidden="true">·</span>
            <x-ui.badge :tone="$book->status->tone()">{{ $book->status->label() }}</x-ui.badge>
        </x-slot:meta>
        <x-slot:actions>
            @can('update', $book)
                <x-ui.button variant="outline" :href="route('books.edit', $book)" icon="heroicon-o-pencil-square">Ubah</x-ui.button>
            @endcan
            @can('delete', $book)
                <x-shared.delete-button :action="route('books.destroy', $book)" :name="$book->title" title="Hapus buku?" size="md" />
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="order-2 space-y-6 lg:order-1 lg:col-span-2">
            <x-ui.card title="Informasi Buku">
                <x-shared.detail-list>
                    <x-shared.detail-item label="Judul Buku" wide>{{ $book->title }}</x-shared.detail-item>
                    <x-shared.detail-item label="Tahun Terbit">{{ $book->year }}</x-shared.detail-item>
                    <x-shared.detail-item label="Penerbit" wide>{{ $book->publisher }}</x-shared.detail-item>
                </x-shared.detail-list>
            </x-ui.card>

            <x-ui.card title="Penulis">
                <x-shared.contributors-list :contributors="$book->contributors" lead-label="Penulis pertama" />
            </x-ui.card>

            <p class="text-xs text-muted">
                Dibuat oleh {{ $book->creator->name }} pada {{ $book->created_at->translatedFormat('d F Y') }}@if ($book->updater),
                diperbarui oleh {{ $book->updater->name }} pada {{ $book->updated_at->translatedFormat('d F Y') }}@endif.
            </p>
        </div>

        <div class="order-1 space-y-6 lg:order-2">
            <x-shared.verification-panel :record="$book" />

            <x-ui.card title="Dokumen Pendukung">
                <x-shared.documents-list :documents="$book->documents" />
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
