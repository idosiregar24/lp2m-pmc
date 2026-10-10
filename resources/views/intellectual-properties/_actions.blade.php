{{-- Row actions, shared by the table and the phone cards. --}}
<div class="inline-flex items-center gap-1">
    <a href="{{ route('intellectual-properties.show', $intellectualProperty) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Lihat {{ $intellectualProperty->title }}">
        <x-heroicon-o-eye class="size-4" />
    </a>
    @can('update', $intellectualProperty)
        <a href="{{ route('intellectual-properties.edit', $intellectualProperty) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Ubah {{ $intellectualProperty->title }}">
            <x-heroicon-o-pencil-square class="size-4" />
        </a>
    @endcan
    @can('delete', $intellectualProperty)
        <x-shared.delete-button :action="route('intellectual-properties.destroy', $intellectualProperty)" :name="$intellectualProperty->title" title="Hapus HKI?" icon-only />
    @endcan
</div>
