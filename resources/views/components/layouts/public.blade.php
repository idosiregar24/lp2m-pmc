{{-- Public portal (no login): header with logo + Masuk, clean canvas, PMC colour strip. --}}
@props(['title' => null])

@php($logoUrl = \App\Support\Settings::logoUrl() ?: asset('images/logo-pmc.png'))

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}LPPM Universitas Pekanbaru Medical Center</title>
    @if ($logoUrl)
        <link rel="icon" href="{{ $logoUrl }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-ink antialiased">
    <header class="sticky top-0 z-30 border-b border-line bg-surface/95 backdrop-blur shadow-2xs">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('public.lecturers.index') }}" class="flex min-w-0 items-center gap-3 transition hover:opacity-90">
                <x-ui.logo size="md" />
                <span class="min-w-0 leading-tight">
                    <span class="flex items-center gap-2">
                        <span class="truncate text-base font-bold text-ink tracking-tight">LPPM Universitas PMC</span>
                        <span class="hidden rounded px-1.5 py-0.2 text-[9px] font-bold bg-brand-50 text-brand-700 border border-brand-200/70 sm:inline">PORTAL PUBLIK</span>
                    </span>
                    <span class="block truncate text-xs text-muted">Lembaga Penelitian dan Pengabdian kepada Masyarakat</span>
                </span>
            </a>
            <nav class="flex items-center gap-2.5" aria-label="Menu publik">
                <a href="{{ route('public.lecturers.index') }}" class="hidden items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-700 sm:flex">
                    <x-heroicon-o-academic-cap class="size-4" />
                    Direktori Dosen
                </a>
                @auth
                    <x-ui.button :href="route('dashboard')" variant="outline" size="sm" icon="heroicon-o-squares-2x2">Buka Dashboard</x-ui.button>
                @else
                    <x-ui.button :href="route('login')" size="sm" icon="heroicon-o-arrow-right-end-on-rectangle">Masuk Sistem</x-ui.button>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-line bg-surface">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 text-center text-xs text-muted sm:flex-row sm:px-6 sm:text-left lg:px-8">
            <div class="flex items-center gap-2.5">
                <x-ui.logo size="xs" />
                <span>&copy; {{ now()->year }} LPPM Universitas Pekanbaru Medical Center · Data resmi terverifikasi.</span>
            </div>
            <p class="text-muted/75">Ilmu · Pelayanan · Kemanusiaan</p>
        </div>
    </footer>
</body>
</html>
