<x-layouts.app title="Ubah Penelitian" :breadcrumbs="[
    ['label' => 'Penelitian', 'url' => route('researches.index')],
    ['label' => $research->title, 'url' => route('researches.show', $research)],
    ['label' => 'Ubah'],
]">
    <x-shared.page-header title="Ubah Penelitian" :description="$research->title" icon="heroicon-o-beaker" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @include('researches._form', ['action' => route('researches.update', $research), 'method' => 'PUT'])
        </div>

        {{-- Saved documents live outside the form (each delete is its own form). --}}
        <x-ui.card title="Dokumen Tersimpan" class="self-start">
            <x-shared.documents-list :documents="$research->documents" :deletable="auth()->user()->can('update', $research)" />
        </x-ui.card>
    </div>
</x-layouts.app>
