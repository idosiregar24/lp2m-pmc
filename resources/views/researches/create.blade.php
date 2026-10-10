<x-layouts.app title="Tambah Penelitian" :breadcrumbs="[['label' => 'Penelitian', 'url' => route('researches.index')], ['label' => 'Tambah']]">
    <x-shared.page-header title="Tambah Penelitian" description="Isi data penelitian beserta peneliti dan dokumen pendukungnya." icon="heroicon-o-beaker" />

    @include('researches._form', ['action' => route('researches.store'), 'method' => 'POST'])
</x-layouts.app>
