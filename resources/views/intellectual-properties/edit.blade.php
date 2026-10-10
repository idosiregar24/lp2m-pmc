<x-layouts.app title="Ubah HKI" :breadcrumbs="[
    ['label' => 'HKI', 'url' => route('intellectual-properties.index')],
    ['label' => $intellectualProperty->title, 'url' => route('intellectual-properties.show', $intellectualProperty)],
    ['label' => 'Ubah'],
]">
    <x-shared.page-header title="Ubah HKI" :description="$intellectualProperty->title" icon="heroicon-o-light-bulb" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @include('intellectual-properties._form', ['action' => route('intellectual-properties.update', $intellectualProperty), 'method' => 'PUT'])
        </div>

        {{-- Saved documents live outside the form (each delete is its own form). --}}
        <x-ui.card title="Dokumen Tersimpan" class="self-start">
            <x-shared.documents-list :documents="$intellectualProperty->documents" :deletable="auth()->user()->can('update', $intellectualProperty)" />
        </x-ui.card>
    </div>
</x-layouts.app>
