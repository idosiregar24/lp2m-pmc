<x-layouts.app :title="$research->title" :breadcrumbs="[['label' => 'Penelitian', 'url' => route('researches.index')], ['label' => $research->title]]">
    <x-shared.page-header :title="$research->title" icon="heroicon-o-beaker">
        <x-slot:meta>
            <span class="tabular-nums">{{ $research->year }}</span>
            <span aria-hidden="true">·</span>
            <x-ui.badge :tone="$research->status->tone()">{{ $research->status->label() }}</x-ui.badge>
        </x-slot:meta>
        <x-slot:actions>
            @can('update', $research)
                <x-ui.button variant="outline" :href="route('researches.edit', $research)" icon="heroicon-o-pencil-square">Ubah</x-ui.button>
            @endcan
            @can('delete', $research)
                <x-shared.delete-button :action="route('researches.destroy', $research)" :name="$research->title" title="Hapus penelitian?" size="md" />
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="order-2 space-y-6 lg:order-1 lg:col-span-2">
            <x-ui.card title="Informasi Penelitian">
                <x-shared.detail-list>
                    <x-shared.detail-item label="Judul Penelitian" wide>{{ $research->title }}</x-shared.detail-item>
                    <x-shared.detail-item label="Tahun">{{ $research->year }}</x-shared.detail-item>
                    <x-shared.detail-item label="Jenis Skema">{{ $research->scheme->name }}</x-shared.detail-item>
                    <x-shared.detail-item label="Sumber Dana">{{ $research->fundingSource->name }}</x-shared.detail-item>
                </x-shared.detail-list>
            </x-ui.card>

            <x-ui.card title="Peneliti">
                <x-shared.contributors-list :contributors="$research->contributors" lead-label="Ketua" />
            </x-ui.card>

            <p class="text-xs text-muted">
                Dibuat oleh {{ $research->creator->name }} pada {{ $research->created_at->translatedFormat('d F Y') }}@if ($research->updater),
                diperbarui oleh {{ $research->updater->name }} pada {{ $research->updated_at->translatedFormat('d F Y') }}@endif.
            </p>
        </div>

        <div class="order-1 space-y-6 lg:order-2">
            <x-shared.verification-panel :record="$research" />

            <x-ui.card title="Dokumen Pendukung">
                <x-shared.documents-list :documents="$research->documents" />
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
