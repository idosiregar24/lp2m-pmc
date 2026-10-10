{{-- Data Master hub: page header + tabs. Expects $createRoute, $createLabel. --}}
<x-shared.page-header title="Data Master" description="Daftar acuan yang dipilih di form. Data yang sudah dipakai dinonaktifkan, bukan dihapus." icon="heroicon-o-circle-stack">
    <x-slot:actions>
        <x-ui.button :href="route($createRoute)" icon="heroicon-o-plus" class="w-full sm:w-auto">{{ $createLabel }}</x-ui.button>
    </x-slot:actions>
</x-shared.page-header>

<x-ui.tabs :items="[
    ['label' => 'Fakultas', 'url' => route('master.faculties.index'), 'active' => request()->routeIs('master.faculties.*')],
    ['label' => 'Prodi', 'url' => route('master.study-programs.index'), 'active' => request()->routeIs('master.study-programs.*')],
    ['label' => 'Skema', 'url' => route('master.schemes.index'), 'active' => request()->routeIs('master.schemes.*')],
    ['label' => 'Sumber Dana', 'url' => route('master.funding-sources.index'), 'active' => request()->routeIs('master.funding-sources.*')],
]" />
