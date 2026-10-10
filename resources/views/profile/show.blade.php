<x-layouts.app title="Profil Saya">
    <x-shared.page-header title="Profil Saya" description="Data diri Anda. Hubungi admin BP2M bila ada yang perlu diperbaiki." icon="heroicon-o-user-circle" />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-ui.card title="Data Diri" class="lg:col-span-2">
            <x-shared.detail-list>
                <x-shared.detail-item label="Nama" wide>{{ $user->name }}</x-shared.detail-item>
                <x-shared.detail-item label="NIDN/NUPTK">{{ $user->nidn_nuptk }}</x-shared.detail-item>
                <x-shared.detail-item label="Username">{{ $user->username }}</x-shared.detail-item>
                <x-shared.detail-item label="Fakultas">{{ $user->studyProgram?->faculty?->name }}</x-shared.detail-item>
                <x-shared.detail-item label="Program Studi">{{ $user->studyProgram?->displayName() }}</x-shared.detail-item>
                <x-shared.detail-item label="Role"><x-ui.badge :tone="$user->role->tone()">{{ $user->role->label() }}</x-ui.badge></x-shared.detail-item>
                <x-shared.detail-item label="Terakhir masuk">{{ $user->last_login_at?->translatedFormat('d F Y, H:i') }}</x-shared.detail-item>
            </x-shared.detail-list>
        </x-ui.card>

        <x-ui.card title="Ganti Password">
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4" x-data="{ busy: false }" @submit="busy = true">
                @csrf
                @method('PUT')

                <x-form.field name="current_password" label="Password lama" required>
                    <x-form.input name="current_password" type="password" autocomplete="current-password" required />
                </x-form.field>
                <x-form.field name="password" label="Password baru" hint="Minimal 8 karakter." required>
                    <x-form.input name="password" type="password" autocomplete="new-password" minlength="8" required />
                </x-form.field>
                <x-form.field name="password_confirmation" label="Ulangi password baru" required>
                    <x-form.input name="password_confirmation" type="password" autocomplete="new-password" required />
                </x-form.field>

                <x-ui.button class="w-full" x-bind:disabled="busy">Simpan Password</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>
