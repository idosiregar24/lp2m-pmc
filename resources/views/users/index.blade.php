<x-layouts.app title="Pengguna">
    <x-shared.page-header title="Pengguna" description="Akun admin BP2M dan dosen. Akun yang sudah punya data dinonaktifkan, bukan dihapus." icon="heroicon-o-users">
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button :href="route('users.import')" variant="outline" icon="heroicon-o-arrow-up-tray" class="w-full sm:w-auto">Impor Excel</x-ui.button>
                <x-ui.button :href="route('users.create')" icon="heroicon-o-plus" class="w-full sm:w-auto">Tambah Pengguna</x-ui.button>
            </div>
        </x-slot:actions>
    </x-shared.page-header>

    <x-ui.card flush>
        <x-shared.filter-bar :action="route('users.index')">
            <x-slot:search>
                <x-form.label for="q" class="sr-only">Cari</x-form.label>
                <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari nama, NIDN/NUPTK, username…" />
            </x-slot:search>
            <div>
                <x-form.label for="faculty" class="sr-only">Fakultas</x-form.label>
                <x-form.select name="faculty" :options="$faculties" :value="$filters['faculty'] ?? null" placeholder="Semua fakultas" />
            </div>
            <div>
                <x-form.label for="study_program" class="sr-only">Prodi</x-form.label>
                <x-form.select name="study_program" :options="$studyPrograms" :value="$filters['study_program'] ?? null" placeholder="Semua prodi" />
            </div>
            <div>
                <x-form.label for="role" class="sr-only">Role</x-form.label>
                <x-form.select name="role" :options="$roles" :value="$filters['role'] ?? null" placeholder="Semua role" />
            </div>
            <div>
                <x-form.label for="status" class="sr-only">Status</x-form.label>
                <x-form.select name="status" :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$filters['status'] ?? null" placeholder="Semua status" />
            </div>
        </x-shared.filter-bar>

        @if ($users->isEmpty())
            <x-ui.empty-state icon="heroicon-o-users" title="Tidak ada pengguna yang cocok." />
        @else
            <ul class="divide-y divide-line md:hidden">
                @foreach ($users as $user)
                    <li class="flex items-start justify-between gap-3 p-4">
                        <div class="min-w-0">
                            <a href="{{ route('users.show', $user) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $user->name }}</a>
                            <p class="text-xs text-muted">{{ '@'.$user->username }}{{ $user->nidn_nuptk ? ' · '.$user->nidn_nuptk : '' }}</p>
                            <p class="text-xs text-muted">{{ $user->studyProgram?->displayName() }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <x-ui.badge :tone="$user->role->tone()">{{ $user->role->label() }}</x-ui.badge>
                            <x-ui.badge :tone="$user->is_active ? 'success' : 'neutral'">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                        </div>
                    </li>
                @endforeach
            </ul>

            <x-ui.table class="hidden md:block">
                <x-slot:head>
                    <tr>
                        <th>Nama</th>
                        <th>NIDN/NUPTK</th>
                        <th>Username</th>
                        <th>Fakultas · Prodi</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-right"><span class="sr-only">Aksi</span></th>
                    </tr>
                </x-slot:head>
                @foreach ($users as $user)
                    <tr>
                        <td><a href="{{ route('users.show', $user) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $user->name }}</a></td>
                        <td class="tabular-nums text-muted">{{ $user->nidn_nuptk ?? '—' }}</td>
                        <td class="text-muted">{{ $user->username }}</td>
                        <td>
                            @if ($user->studyProgram)
                                <span class="block">{{ $user->studyProgram->faculty->name }}</span>
                                <span class="text-xs text-muted">{{ $user->studyProgram->displayName() }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><x-ui.badge :tone="$user->role->tone()">{{ $user->role->label() }}</x-ui.badge></td>
                        <td><x-ui.badge :tone="$user->is_active ? 'success' : 'neutral'">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                        <td class="text-right">
                            <a href="{{ route('users.edit', $user) }}" class="inline-flex rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Ubah {{ $user->name }}">
                                <x-heroicon-o-pencil-square class="size-4" />
                            </a>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $users->links() }}
    </x-ui.card>
</x-layouts.app>
