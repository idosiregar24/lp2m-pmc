{{-- A small form in one card with Batal / Simpan footer (master data forms). --}}
@props([
    'title',
    'action',
    'method' => 'POST',
    'cancel',
])

<form method="POST" action="{{ $action }}" {{ $attributes->class('max-w-2xl') }} x-data="{ busy: false }" @submit="busy = true">
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <x-ui.card :title="$title">
        <div class="space-y-5">
            {{ $slot }}
        </div>

        <x-slot:footer>
            <x-ui.button variant="ghost" :href="$cancel">Batal</x-ui.button>
            <x-ui.button icon="heroicon-o-check" x-bind:disabled="busy">Simpan</x-ui.button>
        </x-slot:footer>
    </x-ui.card>
</form>
