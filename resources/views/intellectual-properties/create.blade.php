<x-layouts.app title="Tambah HKI" :breadcrumbs="[['label' => 'HKI', 'url' => route('intellectual-properties.index')], ['label' => 'Tambah']]">
    <x-shared.page-header title="Tambah HKI" description="Isi data HKI beserta pemilik ki dan dokumen pendukungnya." icon="heroicon-o-light-bulb" />

    @include('intellectual-properties._form', ['action' => route('intellectual-properties.store'), 'method' => 'POST'])
</x-layouts.app>
