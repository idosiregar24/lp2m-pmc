{{-- University logo uploaded in Pengaturan (PRD §4.12); official PMC logo by default. --}}
@props(['size' => 'md'])

@php
    $sizes = [
        'xs' => 'size-7 rounded-md text-[0.55rem]',
        'sm' => 'size-9 rounded-lg text-[0.6rem]',
        'md' => 'size-12 rounded-xl text-xs',
        'lg' => 'size-16 rounded-2xl text-sm',
        'xl' => 'size-20 rounded-2xl text-base',
    ];
    $logoUrl = \App\Support\Settings::logoUrl() ?: asset('images/logo-pmc.png');
@endphp

@if ($logoUrl)
    <img src="{{ $logoUrl }}" alt="Logo Universitas PMC"
        {{ $attributes->class(['shrink-0 object-contain drop-shadow-xs', $sizes[$size] ?? $sizes['md']]) }}>
@else
    <span aria-label="Logo Universitas PMC"
        {{ $attributes->class(['flex shrink-0 items-center justify-center bg-brand-600 font-bold tracking-wide text-gold-400', $sizes[$size] ?? $sizes['md']]) }}>
        PMC
    </span>
@endif
