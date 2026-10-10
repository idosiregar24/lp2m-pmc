<x-layouts.app title="Tambah Pengabdian" :breadcrumbs="[['label' => 'Pengabdian', 'url' => route('community-services.index')], ['label' => 'Tambah']]">
    <x-shared.page-header title="Tambah Pengabdian" description="Isi data pengabdian beserta pengabdi dan dokumen pendukungnya." icon="heroicon-o-user-group" />

    @include('community-services._form', ['action' => route('community-services.store'), 'method' => 'POST'])
</x-layouts.app>
