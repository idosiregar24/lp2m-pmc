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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" x-data="{ sidebarOpen: false }">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-surface focus:px-4 focus:py-2">Lewati ke konten</a>

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-ink/30 backdrop-blur-[1px] lg:hidden" @click="sidebarOpen = false"></div>

    {{-- Sidebar: Rich PMC Institutional Crimson with high-contrast white & gold accents --}}
    <aside x-cloak
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-[#7a0d0d] text-white transition-transform max-lg:border-r max-lg:border-[#630909] lg:translate-x-0 shadow-lg"
        x-trap.noscroll.inert="sidebarOpen && window.innerWidth < 1024"
        @keydown.escape.window="sidebarOpen = false">
        <div class="p-3">
            <div class="flex items-center gap-3 rounded-xl border border-white/15 bg-[#630909]/80 p-2.5 shadow-xs">
                <x-ui.logo size="sm" />
                <div class="min-w-0 leading-tight">
                    <div class="flex items-center gap-1.5">
                        <span class="truncate text-sm font-bold tracking-tight text-white">LPPM</span>
                        <span class="rounded px-1.5 py-0.2 text-[9px] font-bold bg-gold-400 text-gold-950">PMC</span>
                    </div>
                    <p class="truncate text-xs text-brand-100/90">Universitas PMC</p>
                </div>
                <button type="button" class="ml-auto rounded-md p-1 text-white/80 hover:bg-white/10 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                    <x-heroicon-o-x-mark class="size-5" />
                </button>
            </div>
        </div>

        <nav class="flex-1 space-y-4 overflow-y-auto px-3 pb-4 pt-1" aria-label="Menu utama">
            @foreach ($navigation as $group)
                <div @class(['border-t border-white/10 pt-3.5' => ! $loop->first])>
                    @if ($group['label'])
                        <p class="mb-1.5 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-brand-200/90">{{ $group['label'] }}</p>
                    @endif
                    <ul class="space-y-0.5">
                        @foreach ($group['items'] as $item)
                            <li>
                                @if ($item['url'])
                                    <a href="{{ $item['url'] }}"
                                        @class([
                                            'flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs font-medium transition-colors',
                                            'bg-white text-brand-900 font-bold shadow-xs' => $item['active'],
                                            'border border-transparent text-brand-100/90 hover:bg-white/10 hover:text-white' => ! $item['active'],
                                        ])
                                        @if ($item['active']) aria-current="page" @endif>
                                        <x-dynamic-component :component="$item['icon']" @class([
                                            'size-4 shrink-0 transition-colors',
                                            'text-brand-700' => $item['active'],
                                            'text-brand-200/80 group-hover:text-white' => ! $item['active'],
                                        ]) aria-hidden="true" />
                                        <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                        @if ($item['badge'])
                                            <span @class([
                                                'rounded-full px-2 py-0.5 text-[10px] font-bold leading-none',
                                                'bg-brand-100 text-brand-900' => $item['active'],
                                                'bg-gold-400 text-gold-950 shadow-2xs' => ! $item['active'],
                                            ])>{{ $item['badge'] }}</span>
                                        @endif
                                    </a>
                                @else
                                    <span class="flex cursor-not-allowed items-center gap-2.5 rounded-lg border border-transparent px-2.5 py-2 text-xs text-white/40" aria-disabled="true">
                                        <x-dynamic-component :component="$item['icon']" class="size-4 shrink-0 text-white/30" aria-hidden="true" />
                                        <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                        <span class="rounded-full border border-white/20 bg-white/10 px-1.5 py-0.2 text-[10px] text-white/70">Segera</span>
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        {{-- User menu --}}
        <div class="relative border-t border-white/10 p-3" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
            <div x-show="open" x-cloak x-transition.origin.bottom role="menu"
                class="absolute inset-x-3 bottom-full mb-2 overflow-hidden rounded-xl border border-line bg-surface p-1 shadow-xl text-ink">
                <a href="{{ route('profile.show') }}" role="menuitem" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                    <x-heroicon-o-user-circle class="size-4 text-brand-600" /> Profil Saya
                </a>
                <a href="{{ route('public.lecturers.index') }}" role="menuitem" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                    <x-heroicon-o-arrow-top-right-on-square class="size-4 text-brand-600" /> Portal Publik
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" role="menuitem" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-xs font-medium text-slate-700 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="size-4 text-slate-400" /> Keluar
                    </button>
                </form>
            </div>
            <button type="button" class="flex w-full items-center gap-2.5 rounded-lg p-1.5 text-left text-white hover:bg-white/10 transition-colors" @click="open = ! open" :aria-expanded="open" aria-haspopup="menu">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full border border-white/30 bg-white/20 text-xs font-bold text-white shadow-2xs">
                    {{ $user->initials() }}
                </span>
                <span class="min-w-0 flex-1 leading-tight">
                    <span class="block truncate text-xs font-semibold text-white">{{ $user->name }}</span>
                    <span class="inline-flex items-center rounded bg-white/20 px-1.5 py-0.2 text-[10px] font-medium text-white border border-white/20 mt-0.5">{{ $user->role->label() }}</span>
                </span>
                <x-heroicon-o-chevron-up-down class="size-4 shrink-0 text-white/70" />
            </button>
        </div>
    </aside>

    {{-- Content: Cloudflare / shadcn clean floating panel --}}
    <div class="lg:py-2.5 lg:pl-64 lg:pr-2.5">
        <div class="min-h-screen bg-surface lg:min-h-[calc(100vh-1.25rem)] lg:rounded-xl lg:border lg:border-line shadow-2xs">
            <header class="sticky top-0 z-20 flex h-14 items-center gap-3 border-b border-line bg-surface/90 px-4 backdrop-blur lg:static lg:border-0 lg:bg-transparent lg:px-8 lg:pt-3.5 lg:backdrop-blur-none">
                <button type="button" class="-ml-1 rounded-md p-1.5 text-slate-500 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                    <x-heroicon-o-bars-3 class="size-5" />
                </button>
                <span class="text-sm font-bold text-ink lg:hidden">LPPM</span>

                <nav class="hidden min-w-0 flex-1 items-center gap-1.5 text-xs lg:flex" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="font-medium text-slate-500 hover:text-brand-700 transition-colors">Beranda</a>
                    @foreach ($breadcrumbs as $crumb)
                        <x-heroicon-o-chevron-right class="size-3 shrink-0 text-slate-400" aria-hidden="true" />
                        @if (! $loop->last && ! empty($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="truncate font-medium text-slate-500 hover:text-brand-700 transition-colors">{{ $crumb['label'] }}</a>
                        @else
                            <span class="truncate font-semibold text-ink" @if ($loop->last) aria-current="page" @endif>{{ \Illuminate\Support\Str::limit($crumb['label'], 60) }}</span>
                        @endif
                    @endforeach
                    @if (empty($breadcrumbs) && $title && $title !== 'Dashboard')
                        <x-heroicon-o-chevron-right class="size-3 shrink-0 text-slate-400" aria-hidden="true" />
                        <span class="truncate font-semibold text-ink" aria-current="page">{{ $title }}</span>
                    @endif
                </nav>

                {{-- Header quick action: Portal Publik --}}
                <div class="ml-auto hidden items-center gap-2 sm:flex">
                    <a href="{{ route('public.lecturers.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-brand-200/80 bg-brand-50/50 px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-100 hover:border-brand-300 shadow-2xs">
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
