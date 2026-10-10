@props([
    'title' => null,
    'subtitle' => null,
])

@php($logoUrl = \App\Support\Settings::logoUrl() ?: asset('images/logo-pmc.png'))

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
<body class="min-h-screen bg-canvas text-ink antialiased">
    {{-- PMC colour strip --}}
    <div class="fixed inset-x-0 top-0 h-1.5 bg-linear-to-r from-brand-600 via-navy-700 to-gold-400" aria-hidden="true"></div>
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 flex flex-col items-center text-center">
                <x-ui.logo size="lg" />
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-ink">Sistem Informasi LPPM</h1>
                <p class="mt-1 text-xs text-muted">Universitas Pekanbaru Medical Center</p>
            </div>

            <div class="rounded-3xl border border-line bg-surface p-6 shadow-sm sm:p-8">
                @if ($title)
                    <h2 class="text-base font-bold text-ink">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-sm text-muted">{{ $subtitle }}</p>
                @endif

                @if (session('success'))
                    <x-ui.alert tone="success" class="mt-4">{{ session('success') }}</x-ui.alert>
                @endif

                <div @class(['mt-6' => $title || $subtitle])>
                    {{ $slot }}
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-muted">&copy; {{ now()->year }} LPPM Universitas PMC</p>
        </div>
    </main>
</body>
</html>
