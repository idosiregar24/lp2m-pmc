{{-- Delete with a confirmation modal (no browser confirm()). --}}
@props([
    'action',
    'name' => null,            // what is being deleted, shown in the dialog
    'label' => 'Hapus',
    'title' => 'Hapus data?',
    'iconOnly' => false,
    'size' => 'sm',
])

<div x-data="{ open: false }" class="inline-flex">
    @if ($iconOnly)
        <button type="button" @click="open = true" aria-label="{{ $label }}{{ $name ? ' '.$name : '' }}"
            {{ $attributes->class('rounded-md p-1.5 text-muted hover:bg-danger-soft hover:text-danger-ink') }}>
            <x-heroicon-o-trash class="size-4" />
        </button>
    @else
        <x-ui.button type="button" variant="outline-danger" :size="$size" icon="heroicon-o-trash" x-on:click="open = true"
            {{ $attributes }}>{{ $label }}</x-ui.button>
    @endif

    <x-ui.modal :title="$title">
        <form method="POST" action="{{ $action }}" x-data="{ busy: false }" @submit="busy = true">
            @csrf
            @method('DELETE')
            <div class="px-5 py-4 text-sm text-muted">
                @if ($name)
                    <p><span class="font-medium text-ink">“{{ \Illuminate\Support\Str::limit($name, 120) }}”</span> akan dihapus.</p>
                @endif
                <p class="mt-1">Tindakan ini tidak dapat dibatalkan dari halaman ini.</p>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-line px-5 py-3 sm:flex-row sm:justify-end">
                <x-ui.button type="button" variant="ghost" x-on:click="open = false">Batal</x-ui.button>
                <x-ui.button variant="danger" icon="heroicon-o-trash" x-bind:disabled="busy">{{ $label }}</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
