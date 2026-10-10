<x-layouts.app title="Tambah Buku" :breadcrumbs="[['label' => 'Buku', 'url' => route('books.index')], ['label' => 'Tambah']]">
    <x-shared.page-header title="Tambah Buku" description="Isi data buku beserta penulis dan dokumen pendukungnya." icon="heroicon-o-book-open" />

    @include('books._form', ['action' => route('books.store'), 'method' => 'POST'])
</x-layouts.app>
