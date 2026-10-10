<x-layouts.app title="Buku">
    <x-shared.page-header title="Buku" description="Buku karya dosen Universitas PMC." icon="heroicon-o-book-open">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('books.export', $filters)" icon="heroicon-o-arrow-down-tray" class="w-full sm:w-auto">Ekspor Excel</x-ui.button>
            @can('create', App\Models\Book::class)
                <x-ui.button :href="route('books.create')" icon="heroicon-o-plus" class="w-full sm:w-auto">Tambah Buku</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <x-ui.card flush>
        @include('books._filters')

        @if ($books->isEmpty())
            @if (array_filter($filters))
                <x-ui.empty-state icon="heroicon-o-book-open" title="Tidak ada data yang cocok dengan filter.">
                    <x-ui.button variant="ghost" :href="route('books.index')">Reset filter</x-ui.button>
                </x-ui.empty-state>
            @else
                <x-ui.empty-state icon="heroicon-o-book-open" title="Belum ada data buku." description="Data yang Anda input atau yang mencantumkan Anda sebagai anggota akan tampil di sini.">
                    @can('create', App\Models\Book::class)
                        <x-ui.button :href="route('books.create')" icon="heroicon-o-plus">Tambah Buku</x-ui.button>
                    @endcan
                </x-ui.empty-state>
            @endif
        @else
            {{-- Phone: cards --}}
            <ul class="divide-y divide-line md:hidden">
                @foreach ($books as $book)
                    <li class="space-y-2 p-4">
                        <a href="{{ route('books.show', $book) }}" class="block font-medium text-ink underline-offset-4 hover:underline">{{ $book->title }}</a>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span>{{ $book->year }}</span>
                            <x-ui.badge :tone="$book->status->tone()">{{ $book->status->label() }}</x-ui.badge>
                            <span>{{ $book->publisher }}</span>
                        </div>
                        <p class="text-sm text-muted">
                            {{ $book->leadContributor?->displayName() }}{{ $book->contributors_count > 1 ? ', dkk.' : '' }}
                        </p>
                        @include('books._actions')
                    </li>
                @endforeach
            </ul>

            {{-- Tablet & desktop: table --}}
            <x-ui.table class="hidden md:block">
                <x-slot:head>
                    <tr>
                        <th class="min-w-64">Judul</th>
                        <th>Tahun</th>
                        <th class="min-w-40">Penulis Pertama</th>
                        <th class="min-w-48">Penerbit</th>
                        <th class="hidden xl:table-cell">Dokumen</th>
                        <th>Status</th>
                        <th class="text-right"><span class="sr-only">Aksi</span></th>
                    </tr>
                </x-slot:head>
                @foreach ($books as $book)
                    <tr>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $book->title }}</a>
                        </td>
                        <td class="tabular-nums">{{ $book->year }}</td>
                        <td>
                            {{ $book->leadContributor?->displayName() }}
                            @if ($book->contributors_count > 1)
                                <span class="text-muted">dkk.</span>
                            @endif
                        </td>
                        <td>{{ $book->publisher }}</td>
                        <td class="hidden tabular-nums text-muted xl:table-cell">{{ $book->documents_count }}</td>
                        <td><x-ui.badge :tone="$book->status->tone()">{{ $book->status->label() }}</x-ui.badge></td>
                        <td class="whitespace-nowrap text-right">@include('books._actions')</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $books->links() }}
    </x-ui.card>
</x-layouts.app>
