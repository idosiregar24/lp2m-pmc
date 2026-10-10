{{-- Row actions, shared by the table and the phone cards. --}}
<div class="inline-flex items-center gap-1">
    <a href="{{ route('community-services.show', $communityService) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Lihat {{ $communityService->title }}">
        <x-heroicon-o-eye class="size-4" />
    </a>
    @can('update', $communityService)
        <a href="{{ route('community-services.edit', $communityService) }}" class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Ubah {{ $communityService->title }}">
            <x-heroicon-o-pencil-square class="size-4" />
        </a>
    @endcan
    @can('delete', $communityService)
        <x-shared.delete-button :action="route('community-services.destroy', $communityService)" :name="$communityService->title" title="Hapus pengabdian?" icon-only />
    @endcan
</div>
