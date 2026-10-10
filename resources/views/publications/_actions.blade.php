{{-- Row actions, shared by the table and the phone cards. --}}
<div class="inline-flex items-center gap-1">
    <a href="{{ route('publications.show', $publication) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Lihat {{ $publication->title }}">
        <x-heroicon-o-eye class="size-4" />
    </a>
    @can('update', $publication)
        <a href="{{ route('publications.edit', $publication) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Ubah {{ $publication->title }}">
            <x-heroicon-o-pencil-square class="size-4" />
        </a>
    @endcan
    @can('delete', $publication)
        <x-shared.delete-button :action="route('publications.destroy', $publication)" :name="$publication->title" title="Hapus publikasi?" icon-only />
    @endcan
</div>
