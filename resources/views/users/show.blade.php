<x-layouts.app :title="$user->name" :breadcrumbs="[['label' => 'Pengguna', 'url' => route('users.index')], ['label' => $user->name]]">
    <x-shared.page-header :title="$user->name" icon="heroicon-o-users">
        <x-slot:meta>
            <x-ui.badge :tone="$user->role->tone()">{{ $user->role->label() }}</x-ui.badge>
            <x-ui.badge :tone="$user->is_active ? 'success' : 'neutral'">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
            @if ($user->must_change_password)
                <x-ui.badge tone="warning">Password sementara</x-ui.badge>
            @endif
        </x-slot:meta>
        <x-slot:actions>
            <x-ui.button variant="outline" :href="route('users.edit', $user)" icon="heroicon-o-pencil-square">Ubah</x-ui.button>
        </x-slot:actions>
    </x-shared.page-header>

    @if (session('generated_password'))
        <x-ui.alert tone="warning" title="Password sementara">
            <p>Sampaikan kepada pengguna secara langsung. Password ini tidak akan ditampilkan lagi:</p>
            <p class="mt-2 inline-block rounded-md bg-surface px-3 py-1.5 font-mono text-base font-semibold tracking-wider text-ink ring-1 ring-line">{{ session('generated_password') }}</p>
        </x-ui.alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-ui.card title="Data Diri">
                <x-shared.detail-list>
                    <x-shared.detail-item label="Nama" wide>{{ $user->name }}</x-shared.detail-item>
                    <x-shared.detail-item label="NIDN/NUPTK">{{ $user->nidn_nuptk }}</x-shared.detail-item>
                    <x-shared.detail-item label="Username">{{ $user->username }}</x-shared.detail-item>
                    <x-shared.detail-item label="Fakultas">{{ $user->studyProgram?->faculty?->name }}</x-shared.detail-item>
                    <x-shared.detail-item label="Program Studi">{{ $user->studyProgram?->displayName() }}</x-shared.detail-item>
                    <x-shared.detail-item label="Terakhir masuk">{{ $user->last_login_at?->translatedFormat('d F Y, H:i') }}</x-shared.detail-item>
                    <x-shared.detail-item label="Dibuat">{{ $user->created_at->translatedFormat('d F Y') }}</x-shared.detail-item>
                </x-shared.detail-list>
            </x-ui.card>

            <x-ui.card title="Ringkasan Data" description="Jumlah data yang dibuat atau mencantumkan pengguna ini (semua status).">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($summary as $item)
                        <div class="rounded-lg border border-line p-4">
                            <p class="text-sm text-muted">{{ $item['label'] }}</p>
                            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ $item['count'] }}</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        <x-ui.card title="Tindakan Akun" class="self-start">
            <div class="space-y-3">
                <form method="POST" action="{{ route('users.reset-password', $user) }}">
                    @csrf
                    @method('PATCH')
                    <x-ui.button variant="secondary" class="w-full" icon="heroicon-o-key">Reset Password</x-ui.button>
                </form>
                <p class="text-xs text-muted">Membuat password sementara baru; pengguna wajib menggantinya saat masuk.</p>

                @can('toggleActive', $user)
                    <form method="POST" action="{{ route('users.toggle-active', $user) }}" class="border-t border-line pt-3">
                        @csrf
                        @method('PATCH')
                        <x-ui.button variant="outline" class="w-full" :icon="$user->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle'">
                            {{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                        </x-ui.button>
                    </form>
                @endcan

                @can('delete', $user)
                    <div class="border-t border-line pt-3">
                        <x-shared.delete-button :action="route('users.destroy', $user)" :name="$user->name" title="Hapus akun?" label="Hapus Akun" size="md" class="w-full" />
                        <p class="mt-2 text-xs text-muted">Hanya untuk akun yang belum punya data sama sekali.</p>
                    </div>
                @endcan
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>
