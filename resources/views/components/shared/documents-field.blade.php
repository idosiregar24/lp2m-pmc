{{-- New supporting documents (PDF). Existing ones are listed/deleted outside the form. --}}
@props([
    'existing' => 0, // documents already attached
])

@php
    $max = (int) config('bp2m.documents.max_files');
    $maxMb = (int) config('bp2m.documents.max_kb') / 1024;
    $remaining = max(0, $max - $existing);
@endphp

<div {{ $attributes->class('space-y-2') }} x-data="{ files: [] }">
    @if ($remaining > 0)
        <x-form.file name="documents[]" id="documents" multiple accept="application/pdf,.pdf"
            x-on:change="files = Array.from($event.target.files).map(f => f.name)" />
        <p class="text-xs text-muted">
            PDF, maks {{ $maxMb }} MB per file, maks {{ $max }} file per data{{ $existing ? " (sisa {$remaining})" : '' }}.
        </p>
        <ul x-show="files.length" x-cloak class="space-y-1 text-xs text-ink">
            <template x-for="name in files" :key="name">
                <li class="flex items-center gap-1.5"><x-heroicon-o-document class="size-4 text-muted" /><span x-text="name"></span></li>
            </template>
        </ul>
    @else
        <x-ui.alert tone="info">Batas {{ $max }} dokumen sudah tercapai. Hapus dokumen lama untuk mengunggah yang baru.</x-ui.alert>
    @endif
    <x-form.error name="documents" />
    @foreach ($errors->get('documents.*') as $messages)
        <p class="text-xs text-danger-ink">{{ $messages[0] }}</p>
    @endforeach
</div>
