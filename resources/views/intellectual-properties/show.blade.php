<x-layouts.app :title="$intellectualProperty->title" :breadcrumbs="[['label' => 'HKI', 'url' => route('intellectual-properties.index')], ['label' => $intellectualProperty->title]]">
    <x-shared.page-header :title="$intellectualProperty->title" icon="heroicon-o-light-bulb">
        <x-slot:meta>
            <span class="tabular-nums">{{ $intellectualProperty->year }}</span>
            <span aria-hidden="true">·</span>
            <x-ui.badge :tone="$intellectualProperty->status->tone()">{{ $intellectualProperty->status->label() }}</x-ui.badge>
        </x-slot:meta>
        <x-slot:actions>
            @can('update', $intellectualProperty)
                <x-ui.button variant="outline" :href="route('intellectual-properties.edit', $intellectualProperty)" icon="heroicon-o-pencil-square">Ubah</x-ui.button>
            @endcan
            @can('delete', $intellectualProperty)
                <x-shared.delete-button :action="route('intellectual-properties.destroy', $intellectualProperty)" :name="$intellectualProperty->title" title="Hapus HKI?" size="md" />
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="order-2 space-y-6 lg:order-1 lg:col-span-2">
            <x-ui.card title="Informasi HKI">
                <x-shared.detail-list>
                    <x-shared.detail-item label="Judul KI" wide>{{ $intellectualProperty->title }}</x-shared.detail-item>
                    <x-shared.detail-item label="Tahun Terbit">{{ $intellectualProperty->year }}</x-shared.detail-item>
                    <x-shared.detail-item label="Jenis KI"><x-ui.badge>{{ $intellectualProperty->type->label() }}</x-ui.badge></x-shared.detail-item>
                </x-shared.detail-list>
            </x-ui.card>

            <x-ui.card title="Pemilik KI">
                <x-shared.contributors-list :contributors="$intellectualProperty->contributors" lead-label="Pemilik pertama" />
            </x-ui.card>

            <p class="text-xs text-muted">
                Dibuat oleh {{ $intellectualProperty->creator->name }} pada {{ $intellectualProperty->created_at->translatedFormat('d F Y') }}@if ($intellectualProperty->updater),
                diperbarui oleh {{ $intellectualProperty->updater->name }} pada {{ $intellectualProperty->updated_at->translatedFormat('d F Y') }}@endif.
            </p>
        </div>

        <div class="order-1 space-y-6 lg:order-2">
            <x-shared.verification-panel :record="$intellectualProperty" />

            <x-ui.card title="Dokumen Pendukung">
                <x-shared.documents-list :documents="$intellectualProperty->documents" />
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
