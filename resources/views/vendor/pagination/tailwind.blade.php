@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 text-sm">
        <p class="text-muted">
            @if ($paginator->total() > 0)
                Menampilkan <span class="font-medium text-ink">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-ink">{{ $paginator->lastItem() }}</span>
                dari <span class="font-medium text-ink">{{ $paginator->total() }}</span> data
            @endif
        </p>

        @if ($paginator->hasPages())
            <ul class="flex items-center gap-1">
                <li>
                    @if ($paginator->onFirstPage())
                        <span class="flex size-9 items-center justify-center rounded-lg text-muted/50" aria-disabled="true" aria-label="Sebelumnya">
                            <x-heroicon-o-chevron-left class="size-4" />
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex size-9 items-center justify-center rounded-lg text-muted hover:bg-canvas hover:text-ink" aria-label="Sebelumnya">
                            <x-heroicon-o-chevron-left class="size-4" />
                        </a>
                    @endif
                </li>

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="hidden px-2 text-muted sm:block" aria-disabled="true">{{ $element }}</li>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li class="{{ $page === $paginator->currentPage() ? '' : 'hidden sm:block' }}">
                                @if ($page === $paginator->currentPage())
                                    <span aria-current="page" class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-brand-600 px-2 font-medium text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-ink hover:bg-canvas" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                <li>
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex size-9 items-center justify-center rounded-lg text-muted hover:bg-canvas hover:text-ink" aria-label="Berikutnya">
                            <x-heroicon-o-chevron-right class="size-4" />
                        </a>
                    @else
                        <span class="flex size-9 items-center justify-center rounded-lg text-muted/50" aria-disabled="true" aria-label="Berikutnya">
                            <x-heroicon-o-chevron-right class="size-4" />
                        </span>
                    @endif
                </li>
            </ul>
        @endif
    </nav>
@endif
