<x-layouts.app title="Tambah Publikasi" :breadcrumbs="[['label' => 'Publikasi', 'url' => route('publications.index')], ['label' => 'Tambah']]">
    <x-shared.page-header title="Tambah Publikasi" description="Isi data publikasi beserta penulis dan dokumen pendukungnya." icon="heroicon-o-newspaper" />

    @include('publications._form', ['action' => route('publications.store'), 'method' => 'POST'])
</x-layouts.app>
