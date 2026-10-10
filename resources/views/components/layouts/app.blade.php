@props([
    'title' => null,
    'breadcrumbs' => [], // [['label' => '…', 'url' => '…'], …] — last item = current page
])

@php
    $user = auth()->user();
    $navigation = \App\Support\Navigation::for($user);
    $logoUrl = \App\Support\Settings::logoUrl();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ? $title.' · ' : '' }}LPPM Universitas PMC</title>
    @if ($logoUrl)
        <link rel="icon" href="{{ $logoUrl }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" x-data="{ sidebarOpen: false }">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-surface focus:px-4 focus:py-2">Lewati ke konten</a>

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-ink/30 backdrop-blur-[1px] lg:hidden" @click="sidebarOpen = false"></div>

    {{-- Sidebar: light grey rail; the active item is a white card --}}
    <aside x-cloak
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-canvas transition-transform max-lg:border-r max-lg:border-line lg:translate-x-0"
        x-trap.noscroll.inert="sidebarOpen && window.innerWidth < 1024"
        @keydown.escape.window="sidebarOpen = false">
        <div class="p-3">
            <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface p-2.5 shadow-xs">
                <x-ui.logo size="sm" />
                <div class="min-w-0 leading-tight">
                    <p class="truncate text-sm font-bold text-ink">LPPM</p>
                    <p class="truncate text-xs text-muted">Universitas PMC</p>
                </div>
                <button type="button" class="ml-auto rounded-md p-1 text-muted hover:bg-canvas hover:text-ink lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                    <x-heroicon-o-x-mark class="size-5" />
                </button>
            </div>
        </div>

        <nav class="flex-1 space-y-5 overflow-y-auto px-3 pb-4 pt-2" aria-label="Menu utama">
            @foreach ($navigation as $group)
                <div @class(['border-t border-line pt-4' => ! $loop->first])>
                    @if ($group['label'])
                        <p class="mb-1.5 px-2.5 text-xs font-medium text-muted uppercase tracking-wider text-[0.65rem]">{{ $group['label'] }}</p>
                    @endif
                    <ul class="space-y-0.5">
                        @foreach ($group['items'] as $item)
                            <li>
                                @if ($item['url'])
                                    <a href="{{ $item['url'] }}"
                                        @class([
                                            'flex items-center gap-2.5 rounded-xl border px-2.5 py-1.5 text-sm transition',
                                            'border-line bg-surface font-semibold text-ink shadow-xs' => $item['active'],
                                            'border-transparent text-ink/75 hover:bg-primary-100/60 hover:text-ink' => ! $item['active'],
                                        ])
                                        @if ($item['active']) aria-current="page" @endif>
                                        <x-dynamic-component :component="$item['icon']" @class(['size-[1.125rem] shrink-0', 'text-brand-600' => $item['active'], 'text-muted' => ! $item['active']]) aria-hidden="true" />
                                        <span class="flex-1">{{ $item['label'] }}</span>
                                        @if ($item['badge'])
                                            <span class="min-w-5 rounded-full bg-brand-600 px-1.5 py-0.5 text-center text-[0.7rem] font-semibold leading-none text-white">{{ $item['badge'] }}</span>
                                        @endif
                                    </a>
                                @else
                                    <span class="flex cursor-not-allowed items-center gap-2.5 rounded-xl border border-transparent px-2.5 py-1.5 text-sm text-muted/70" aria-disabled="true">
                                        <x-dynamic-component :component="$item['icon']" class="size-[1.125rem] shrink-0" aria-hidden="true" />
                                        <span class="flex-1">{{ $item['label'] }}</span>
                                        <span class="rounded-full border border-line bg-surface px-2 py-0.5 text-[0.65rem]">Segera</span>
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        {{-- User menu --}}
        <div class="relative border-t border-line p-3" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
            <div x-show="open" x-cloak x-transition.origin.bottom role="menu"
                class="absolute inset-x-3 bottom-full mb-2 overflow-hidden rounded-xl border border-line bg-surface p-1 shadow-lg">
                <a href="{{ route('profile.show') }}" role="menuitem" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-ink hover:bg-canvas">
                    <x-heroicon-o-user-circle class="size-4 text-muted" /> Profil Saya
                </a>
                <a href="{{ route('public.lecturers.index') }}" role="menuitem" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-ink hover:bg-canvas">
                    <x-heroicon-o-arrow-top-right-on-square class="size-4 text-muted" /> Portal Publik
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" role="menuitem" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-ink hover:bg-canvas">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="size-4 text-muted" /> Keluar
                    </button>
                </form>
            </div>
            <button type="button" class="flex w-full items-center gap-2.5 rounded-xl p-1.5 text-left hover:bg-primary-100/60" @click="open = ! open" :aria-expanded="open" aria-haspopup="menu">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full border border-line bg-surface text-xs font-semibold text-ink">
                    {{ $user->initials() }}
                </span>
                <span class="min-w-0 flex-1 leading-tight">
                    <span class="block truncate text-sm font-medium text-ink">{{ $user->name }}</span>
                    <span class="block text-xs text-muted">{{ $user->role->label() }}</span>
                </span>
                <x-heroicon-o-chevron-up-down class="size-4 shrink-0 text-muted" />
            </button>
        </div>
    </aside>

    {{-- Content: a white panel floating on the grey canvas (desktop), full white on phones --}}
    <div class="lg:py-2 lg:pl-64 lg:pr-2">
        <div class="min-h-screen bg-surface lg:min-h-[calc(100vh-1rem)] lg:rounded-2xl lg:border lg:border-line">
            <header class="sticky top-0 z-20 flex h-14 items-center gap-2 border-b border-line bg-surface/90 px-4 backdrop-blur lg:static lg:border-0 lg:bg-transparent lg:px-8 lg:pt-4 lg:backdrop-blur-none">
                <button type="button" class="-ml-1 rounded-md p-2 text-muted hover:bg-canvas lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                    <x-heroicon-o-bars-3 class="size-5" />
                </button>
                <span class="text-sm font-bold text-ink lg:hidden">LPPM</span>

                <nav class="hidden min-w-0 flex-1 items-center gap-1.5 text-sm lg:flex" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="text-muted hover:text-ink">Beranda</a>
                    @foreach ($breadcrumbs as $crumb)
                        <x-heroicon-o-chevron-right class="size-3.5 shrink-0 text-muted/50" aria-hidden="true" />
                        @if (! $loop->last && ! empty($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="truncate text-muted hover:text-ink">{{ $crumb['label'] }}</a>
                        @else
                            <span class="truncate text-ink" @if ($loop->last) aria-current="page" @endif>{{ \Illuminate\Support\Str::limit($crumb['label'], 60) }}</span>
                        @endif
                    @endforeach
                    @if (empty($breadcrumbs) && $title && $title !== 'Dashboard')
                        <x-heroicon-o-chevron-right class="size-3.5 shrink-0 text-muted/50" aria-hidden="true" />
                        <span class="truncate text-ink" aria-current="page">{{ $title }}</span>
                    @endif
                </nav>

                {{-- Header quick action: Portal Publik --}}
                <div class="ml-auto hidden items-center gap-2 sm:flex">
                    <a href="{{ route('public.lecturers.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-3 py-1.5 text-xs font-semibold text-muted transition hover:border-brand-200 hover:bg-surface hover:text-brand-700">
                        <x-heroicon-o-arrow-top-right-on-square class="size-3.5" />
                        Portal Publik
                    </a>
                </div>
            </header>

            <main id="konten" class="mx-auto max-w-7xl space-y-6 px-4 py-5 lg:px-8 lg:pb-10 lg:pt-3">
                @if (session('success'))
                    <x-ui.alert tone="success">{{ session('success') }}</x-ui.alert>
                @endif
                @if (session('error'))
                    <x-ui.alert tone="danger">{{ session('error') }}</x-ui.alert>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
