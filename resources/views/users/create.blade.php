<x-layouts.app title="Tambah Pengguna" :breadcrumbs="[['label' => 'Pengguna', 'url' => route('users.index')], ['label' => 'Tambah']]">
    <x-shared.page-header title="Tambah Pengguna" description="Akun baru memakai password sementara yang wajib diganti saat pertama masuk." icon="heroicon-o-users" />

    <div class="max-w-3xl">
        @include('users._form', ['action' => route('users.store'), 'method' => 'POST'])
    </div>
</x-layouts.app>
