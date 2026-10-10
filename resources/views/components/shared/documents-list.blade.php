@props([
    'documents',
    'deletable' => false, // show delete buttons (edit page, @can update)
])

@if ($documents->isEmpty())
    <p {{ $attributes->class('text-sm text-muted') }}>Belum ada dokumen pendukung.</p>
@else
    <ul {{ $attributes->class('divide-y divide-line') }}>
        @foreach ($documents as $document)
            <li class="flex items-center gap-3 py-2.5 first:pt-0 last:pb-0">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-line bg-canvas text-ink">
                    <x-heroicon-o-document-text class="size-5" aria-hidden="true" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-ink" title="{{ $document->original_name }}">{{ $document->original_name }}</p>
                    <p class="text-xs text-muted">{{ $document->humanSize() }} · {{ $document->created_at->translatedFormat('d M Y') }}</p>
                </div>
                <a href="{{ route('documents.download', $document) }}"
                    class="rounded-md p-1.5 text-muted hover:bg-canvas hover:text-ink" aria-label="Unduh {{ $document->original_name }}">
                    <x-heroicon-o-arrow-down-tray class="size-4" />
                </a>
                @if ($deletable)
                    <x-shared.delete-button :action="route('documents.destroy', $document)" :name="$document->original_name"
                        title="Hapus dokumen?" icon-only />
                @endif
            </li>
        @endforeach
    </ul>
@endif
