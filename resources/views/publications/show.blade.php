<x-layouts.app :title="$publication->title" :breadcrumbs="[['label' => 'Publikasi', 'url' => route('publications.index')], ['label' => $publication->title]]">
    <x-shared.page-header :title="$publication->title" icon="heroicon-o-newspaper">
        <x-slot:meta>
            <span class="tabular-nums">{{ $publication->year }}</span>
            <span aria-hidden="true">·</span>
            <x-ui.badge :tone="$publication->status->tone()">{{ $publication->status->label() }}</x-ui.badge>
        </x-slot:meta>
        <x-slot:actions>
            @can('update', $publication)
                <x-ui.button variant="outline" :href="route('publications.edit', $publication)" icon="heroicon-o-pencil-square">Ubah</x-ui.button>
            @endcan
            @can('delete', $publication)
                <x-shared.delete-button :action="route('publications.destroy', $publication)" :name="$publication->title" title="Hapus publikasi?" size="md" />
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="order-2 space-y-6 lg:order-1 lg:col-span-2">
            <x-ui.card title="Informasi Publikasi">
                <x-shared.detail-list>
                    <x-shared.detail-item label="Judul Publikasi" wide>{{ $publication->title }}</x-shared.detail-item>
                    <x-shared.detail-item label="Tahun Terbit">{{ $publication->year }}</x-shared.detail-item>
                    <x-shared.detail-item label="Jenis Wadah">{{ $publication->venue_type->label() }}</x-shared.detail-item>
                    <x-shared.detail-item label="Level"><x-ui.badge :tone="$publication->level->tone()">{{ $publication->level->label() }}</x-ui.badge></x-shared.detail-item>
                    <x-shared.detail-item :label="$publication->venue_type->venueLabel()" wide>{{ $publication->venue_name }}</x-shared.detail-item>
                    <x-shared.detail-item label="Penerbit" wide>{{ $publication->publisher }}</x-shared.detail-item>
                </x-shared.detail-list>
            </x-ui.card>

            <x-ui.card title="Penulis">
                <x-shared.contributors-list :contributors="$publication->contributors" lead-label="Penulis pertama" />
            </x-ui.card>

            <p class="text-xs text-muted">
                Dibuat oleh {{ $publication->creator->name }} pada {{ $publication->created_at->translatedFormat('d F Y') }}@if ($publication->updater),
                diperbarui oleh {{ $publication->updater->name }} pada {{ $publication->updated_at->translatedFormat('d F Y') }}@endif.
            </p>
        </div>

        <div class="order-1 space-y-6 lg:order-2">
            <x-shared.verification-panel :record="$publication" />

            <x-ui.card title="Dokumen Pendukung">
                <x-shared.documents-list :documents="$publication->documents" />
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
