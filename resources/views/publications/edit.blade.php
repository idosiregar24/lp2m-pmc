<x-layouts.app title="Ubah Publikasi" :breadcrumbs="[
    ['label' => 'Publikasi', 'url' => route('publications.index')],
    ['label' => $publication->title, 'url' => route('publications.show', $publication)],
    ['label' => 'Ubah'],
]">
    <x-shared.page-header title="Ubah Publikasi" :description="$publication->title" icon="heroicon-o-newspaper" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @include('publications._form', ['action' => route('publications.update', $publication), 'method' => 'PUT'])
        </div>

        {{-- Saved documents live outside the form (each delete is its own form). --}}
        <x-ui.card title="Dokumen Tersimpan" class="self-start">
            <x-shared.documents-list :documents="$publication->documents" :deletable="auth()->user()->can('update', $publication)" />
        </x-ui.card>
    </div>
</x-layouts.app>
