<x-layouts.guest title="Ganti Password" subtitle="Password Anda dibuat oleh admin dan bersifat sementara. Buat password baru untuk melanjutkan.">
    <form method="POST" action="{{ route('password.force') }}" class="space-y-5" x-data="{ busy: false }" @submit="busy = true">
        @csrf
        @method('PUT')

        <x-form.field name="password" label="Password baru" hint="Minimal 8 karakter." required>
            <x-form.input name="password" type="password" autocomplete="new-password" minlength="8" required autofocus />
        </x-form.field>

        <x-form.field name="password_confirmation" label="Ulangi password baru" required>
            <x-form.input name="password_confirmation" type="password" autocomplete="new-password" required />
        </x-form.field>

        <x-ui.button class="w-full" x-bind:disabled="busy">Simpan Password</x-ui.button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
        @csrf
        <button type="submit" class="text-sm text-muted underline decoration-line underline-offset-4 hover:text-ink">Keluar</button>
    </form>
</x-layouts.guest>
