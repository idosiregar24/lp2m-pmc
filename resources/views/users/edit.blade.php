<x-layouts.app title="Ubah Pengguna" :breadcrumbs="[
    ['label' => 'Pengguna', 'url' => route('users.index')],
    ['label' => $user->name, 'url' => route('users.show', $user)],
    ['label' => 'Ubah'],
]">
    <x-shared.page-header title="Ubah Pengguna" :description="$user->name" icon="heroicon-o-users" />

    <div class="max-w-3xl">
        @include('users._form', ['action' => route('users.update', $user), 'method' => 'PUT'])
    </div>
</x-layouts.app>
