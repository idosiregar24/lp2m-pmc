{{-- Row actions, shared by the table and the phone cards. --}}
<div class="inline-flex items-center gap-1">
    <a href="{{ route('researches.show', $research) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Lihat {{ $research->title }}">
        <x-heroicon-o-eye class="size-4" />
    </a>
    @can('update', $research)
        <a href="{{ route('researches.edit', $research) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Ubah {{ $research->title }}">
            <x-heroicon-o-pencil-square class="size-4" />
        </a>
    @endcan
    @can('delete', $research)
        <x-shared.delete-button :action="route('researches.destroy', $research)" :name="$research->title" title="Hapus penelitian?" icon-only />
    @endcan
</div>
