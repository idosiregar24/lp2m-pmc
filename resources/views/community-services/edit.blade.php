<x-layouts.app title="Ubah Pengabdian" :breadcrumbs="[
    ['label' => 'Pengabdian', 'url' => route('community-services.index')],
    ['label' => $communityService->title, 'url' => route('community-services.show', $communityService)],
    ['label' => 'Ubah'],
]">
    <x-shared.page-header title="Ubah Pengabdian" :description="$communityService->title" icon="heroicon-o-user-group" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @include('community-services._form', ['action' => route('community-services.update', $communityService), 'method' => 'PUT'])
        </div>

        {{-- Saved documents live outside the form (each delete is its own form). --}}
        <x-ui.card title="Dokumen Tersimpan" class="self-start">
            <x-shared.documents-list :documents="$communityService->documents" :deletable="auth()->user()->can('update', $communityService)" />
        </x-ui.card>
    </div>
</x-layouts.app>
