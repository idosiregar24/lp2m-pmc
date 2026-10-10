<x-layouts.app title="Pengaturan">
    <x-shared.page-header title="Pengaturan Sistem" description="Identitas tampilan sistem." icon="heroicon-o-cog-6-tooth" />

    <x-ui.card title="Logo Universitas PMC" description="Dipakai di sidebar, halaman masuk, dan ikon tab browser." class="max-w-2xl">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
            <div class="flex flex-col items-center gap-2">
                <x-ui.logo size="lg" />
                <span class="text-xs text-muted">{{ $logoUrl ? 'Logo saat ini' : 'Lambang sementara' }}</span>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="flex-1 space-y-4"
                x-data="{ busy: false }" @submit="busy = true">
                @csrf
                @method('PUT')

                <x-form.field name="logo" label="File logo" required
                    hint="PNG, JPG, atau WebP; maks 1 MB; minimal 128×128 piksel. Disarankan persegi dengan latar transparan.">
                    <x-form.file name="logo" accept="image/png,image/jpeg,image/webp" required />
                </x-form.field>

                <div class="flex flex-wrap gap-2">
                    <x-ui.button icon="heroicon-o-arrow-up-tray" x-bind:disabled="busy">Unggah Logo</x-ui.button>
                </div>
            </form>
        </div>

        @if ($logoUrl)
            <x-slot:footer>
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="remove_logo" value="1">
                    <x-ui.button variant="outline-danger" size="sm" icon="heroicon-o-trash">Hapus logo</x-ui.button>
                </form>
            </x-slot:footer>
        @endif
    </x-ui.card>
</x-layouts.app>
