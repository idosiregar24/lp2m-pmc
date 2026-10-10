{{--
    <x-ui.table>
        <x-slot:head><tr><th>Judul</th>…</tr></x-slot:head>
        <tr><td>…</td></tr>
    </x-ui.table>
--}}
{{-- relative: absolutely positioned children (sr-only labels) must not widen the page. --}}
<div {{ $attributes->class('relative overflow-x-auto') }}>
    <table class="min-w-full divide-y divide-line text-sm">
        <thead class="bg-surface [&_th]:px-4 [&_th]:py-2.5 [&_th]:text-left [&_th]:text-sm [&_th]:font-normal [&_th]:text-muted">
            {{ $head }}
        </thead>
        <tbody class="divide-y divide-line [&_td]:px-4 [&_td]:py-3 [&_td]:align-top [&_tr:hover]:bg-canvas/60">
            {{ $slot }}
        </tbody>
    </table>
</div>
