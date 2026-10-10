<x-layouts.app :title="$communityService->title" :breadcrumbs="[['label' => 'Pengabdian', 'url' => route('community-services.index')], ['label' => $communityService->title]]">
    <x-shared.page-header :title="$communityService->title" icon="heroicon-o-user-group">
        <x-slot:meta>
            <span class="tabular-nums">{{ $communityService->year }}</span>
            <span aria-hidden="true">·</span>
            <x-ui.badge :tone="$communityService->status->tone()">{{ $communityService->status->label() }}</x-ui.badge>
        </x-slot:meta>
        <x-slot:actions>
            @can('update', $communityService)
                <x-ui.button variant="outline" :href="route('community-services.edit', $communityService)" icon="heroicon-o-pencil-square">Ubah</x-ui.button>
            @endcan
            @can('delete', $communityService)
                <x-shared.delete-button :action="route('community-services.destroy', $communityService)" :name="$communityService->title" title="Hapus pengabdian?" size="md" />
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="order-2 space-y-6 lg:order-1 lg:col-span-2">
            <x-ui.card title="Informasi Pengabdian">
                <x-shared.detail-list>
                    <x-shared.detail-item label="Judul Pengabdian" wide>{{ $communityService->title }}</x-shared.detail-item>
                    <x-shared.detail-item label="Tahun">{{ $communityService->year }}</x-shared.detail-item>
                    <x-shared.detail-item label="Jenis Skema">{{ $communityService->scheme->name }}</x-shared.detail-item>
                    <x-shared.detail-item label="Sumber Dana">{{ $communityService->fundingSource->name }}</x-shared.detail-item>
                    <x-shared.detail-item label="Mitra" wide>{{ $communityService->partner }}</x-shared.detail-item>
                </x-shared.detail-list>
            </x-ui.card>

            <x-ui.card title="Pengabdi">
                <x-shared.contributors-list :contributors="$communityService->contributors" lead-label="Ketua" />
            </x-ui.card>

            <p class="text-xs text-muted">
                Dibuat oleh {{ $communityService->creator->name }} pada {{ $communityService->created_at->translatedFormat('d F Y') }}@if ($communityService->updater),
                diperbarui oleh {{ $communityService->updater->name }} pada {{ $communityService->updated_at->translatedFormat('d F Y') }}@endif.
            </p>
        </div>

        <div class="order-1 space-y-6 lg:order-2">
            <x-shared.verification-panel :record="$communityService" />

            <x-ui.card title="Dokumen Pendukung">
                <x-shared.documents-list :documents="$communityService->documents" />
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
