<x-layouts.guest title="Masuk" subtitle="Gunakan username dan password akun LPPM Anda.">
    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ busy: false }" @submit="busy = true">
        @csrf

        <x-form.field name="username" label="Username" required>
            <x-form.input name="username" autocomplete="username" autocapitalize="none" spellcheck="false" required autofocus />
        </x-form.field>

        <x-form.field name="password" label="Password" required>
            <x-form.input name="password" type="password" autocomplete="current-password" required />
        </x-form.field>

        <x-form.checkbox name="remember" label="Ingat saya" />

        <x-ui.button class="w-full" icon="heroicon-o-arrow-right-end-on-rectangle" x-bind:disabled="busy">Masuk</x-ui.button>

        <p class="text-center text-xs text-muted">Lupa password? Hubungi admin LPPM untuk reset.</p>
    </form>

    <a href="{{ route('public.lecturers.index') }}" class="mt-5 flex items-center justify-center gap-1.5 border-t border-line pt-5 text-sm text-muted underline-offset-4 hover:text-ink hover:underline">
        <x-heroicon-o-magnifying-glass class="size-4" aria-hidden="true" />
        Cari data kinerja dosen tanpa masuk
    </a>
</x-layouts.guest>
