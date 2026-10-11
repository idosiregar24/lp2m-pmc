<x-layouts.app title="Dosen / Pengguna">
    <x-shared.page-header title="Dosen / Pengguna" description="Kelola data akun dosen dan penugasan hak akses administrator LPPM Universitas Pekanbaru Medical Center." icon="heroicon-o-users">
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
                    <li class="p-4 space-y-2">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('users.show', $user) }}" class="font-semibold text-ink underline-offset-4 hover:underline">{{ $user->name }}</a>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center gap-0.5 font-mono text-xs font-semibold px-2 py-0.5 rounded bg-stone-100 text-stone-800 border border-stone-200">
                                        <span class="text-stone-400 font-normal">@</span>{{ $user->username }}
                                    </span>
                                    @if ($user->nidn_nuptk)
                                        <span class="text-xs text-muted">NIDN: {{ $user->nidn_nuptk }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-muted">{{ $user->studyProgram?->displayName() ?? '—' }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <x-ui.badge :tone="$user->role->tone()">{{ $user->role->label() }}</x-ui.badge>
                                <x-ui.badge :tone="$user->is_active ? 'success' : 'neutral'">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                            </div>
                        </div>

                        @if (auth()->user()->isSuperAdmin() && ! auth()->user()->is($user))
                            <div class="pt-2 border-t border-line/60 flex items-center justify-between">
                                <span class="text-2xs text-muted">Akses:</span>
                                @if ($user->role === \App\Enums\UserRole::Dosen)
                                    <form method="POST" action="{{ route('users.change-role', $user) }}" onsubmit="return confirm('Beri hak akses Admin kepada {{ addslashes($user->name) }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="role" value="ADMIN">
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md border border-brand-200 text-brand-700 bg-brand-50 hover:bg-brand-100">
                                            <x-heroicon-m-shield-check class="size-3.5 text-brand-600" />
                                            <span>Jadikan Admin</span>
                                        </button>
                                    </form>
                                @elseif ($user->role === \App\Enums\UserRole::Admin)
                                    <form method="POST" action="{{ route('users.change-role', $user) }}" onsubmit="return confirm('Kembalikan {{ addslashes($user->name) }} menjadi Dosen biasa?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="role" value="DOSEN">
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md border border-stone-200 text-muted bg-surface hover:bg-stone-50">
                                            <x-heroicon-m-arrow-path class="size-3.5 text-muted" />
                                            <span>Kembalikan Dosen</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <x-ui.table class="hidden md:block">
                <x-slot:head>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>NIDN/NUPTK</th>
                        <th>Fakultas · Prodi</th>
                        <th>Role & Hak Akses</th>
                        <th>Status</th>
                        <th class="text-right"><span class="sr-only">Aksi</span></th>
                    </tr>
                </x-slot:head>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $user->name }}</a>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 font-mono text-xs font-semibold px-2.5 py-1 rounded-md bg-stone-100 text-stone-800 border border-stone-200 shadow-2xs">
                                <span class="text-stone-400 font-normal">@</span>{{ $user->username }}
                            </span>
                        </td>
                        <td class="tabular-nums text-muted">{{ $user->nidn_nuptk ?? '—' }}</td>
                        <td>
                            @if ($user->studyProgram)
                                <span class="block text-ink font-medium text-xs">{{ $user->studyProgram->faculty->name }}</span>
                                <span class="text-xs text-muted">{{ $user->studyProgram->displayName() }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-col items-start gap-1.5">
                                <x-ui.badge :tone="$user->role->tone()">{{ $user->role->label() }}</x-ui.badge>

                                @if (auth()->user()->isSuperAdmin() && ! auth()->user()->is($user))
                                    @if ($user->role === \App\Enums\UserRole::Dosen)
                                        <form method="POST" action="{{ route('users.change-role', $user) }}" onsubmit="return confirm('Beri hak akses Admin kepada {{ addslashes($user->name) }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="role" value="ADMIN">
                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-0.5 text-2xs font-medium rounded border border-brand-200 text-brand-700 bg-brand-50 hover:bg-brand-100 transition-colors cursor-pointer" title="Jadikan Admin LPPM">
                                                <x-heroicon-m-shield-check class="size-3 text-brand-600" />
                                                <span>Jadikan Admin</span>
                                            </button>
                                        </form>
                                    @elseif ($user->role === \App\Enums\UserRole::Admin)
                                        <form method="POST" action="{{ route('users.change-role', $user) }}" onsubmit="return confirm('Kembalikan {{ addslashes($user->name) }} menjadi Dosen biasa?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="role" value="DOSEN">
                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-0.5 text-2xs font-medium rounded border border-stone-200 text-muted bg-surface hover:bg-stone-50 hover:text-ink transition-colors cursor-pointer" title="Kembalikan ke Dosen">
                                                <x-heroicon-m-arrow-path class="size-3 text-muted" />
                                                <span>Kembalikan Dosen</span>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
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
