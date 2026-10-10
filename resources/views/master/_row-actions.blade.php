{{-- Expects $editUrl, $deleteUrl, $name. --}}
<div class="inline-flex items-center gap-1">
    <a href="{{ $editUrl }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Ubah {{ $name }}">
        <x-heroicon-o-pencil-square class="size-4" />
    </a>
    <x-shared.delete-button :action="$deleteUrl" :name="$name" icon-only />
</div>
