{{--
    Contributor rows (PRD §6.1): internal lecturer (Tom Select) or outside person.
    Posts contributors[i][user_id] | contributors[i][name] + [affiliation]; order = sort_order.
--}}
@props([
    'label',                    // section label, e.g. "Peneliti"
    'lecturers' => [],          // User::lecturerOptions()
    'value' => [],              // [['user_id' => 1], ['name' => 'X', 'affiliation' => 'Y']]
    'leadLabel' => 'Ketua',
    'memberLabel' => 'Anggota',
    'memberStart' => 1,
])

@php
    $rows = old('contributors', $value);
    $hasError = $errors->has('contributors') || $errors->has('contributors.*');
@endphp

<fieldset {{ $attributes->class('space-y-3') }}
    x-data="contributorsField({
        rows: @js(array_values(is_array($rows) ? $rows : [])),
        lecturers: @js($lecturers),
        leadLabel: @js($leadLabel),
        memberLabel: @js($memberLabel),
        memberStart: @js($memberStart),
    })">
    <legend class="sr-only">{{ $label }}</legend>

    <p class="text-sm text-muted">
        Urutan pertama adalah <span class="font-medium text-ink">{{ $leadLabel }}</span>. Pilih
        <span class="font-medium text-ink">Dosen PMC</span> dari daftar, atau
        <span class="font-medium text-ink">Luar PMC</span> untuk mahasiswa, dosen kampus lain, atau instansi.
    </p>

    @if ($hasError)
        <x-ui.alert tone="danger">
            <ul class="list-inside list-disc">
                @foreach (collect($errors->get('contributors'))->merge(collect($errors->get('contributors.*'))->flatten())->unique() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    <ol class="space-y-3">
        <template x-for="(row, index) in rows" :key="row.key">
            <li class="rounded-lg border border-line bg-canvas/50 p-3">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <span class="flex size-6 items-center justify-center rounded-full bg-surface text-xs font-semibold text-ink ring-1 ring-line" x-text="index + 1"></span>
                    <span class="text-sm font-medium text-ink" x-text="label(index)"></span>

                    <div class="ml-auto flex items-center gap-1">
                        <div class="inline-flex rounded-lg border border-line bg-canvas p-0.5 text-xs" role="radiogroup" aria-label="Jenis anggota">
                            <button type="button" role="radio" class="rounded-md px-2.5 py-1"
                                :class="row.type === 'internal' ? 'bg-surface font-medium text-ink shadow-xs ring-1 ring-line' : 'text-muted hover:text-ink'"
                                :aria-checked="row.type === 'internal'" @click="row.type = 'internal'">Dosen PMC</button>
                            <button type="button" role="radio" class="rounded-md px-2.5 py-1"
                                :class="row.type === 'external' ? 'bg-surface font-medium text-ink shadow-xs ring-1 ring-line' : 'text-muted hover:text-ink'"
                                :aria-checked="row.type === 'external'" @click="row.type = 'external'">Luar PMC</button>
                        </div>
                        <button type="button" class="rounded-md p-1.5 text-muted hover:bg-surface hover:text-ink disabled:opacity-30"
                            :disabled="index === 0" @click="move(index, -1)" aria-label="Naikkan urutan">
                            <x-heroicon-o-arrow-up class="size-4" />
                        </button>
                        <button type="button" class="rounded-md p-1.5 text-muted hover:bg-surface hover:text-ink disabled:opacity-30"
                            :disabled="index === rows.length - 1" @click="move(index, 1)" aria-label="Turunkan urutan">
                            <x-heroicon-o-arrow-down class="size-4" />
                        </button>
                        <button type="button" class="rounded-md p-1.5 text-muted hover:bg-danger-soft hover:text-danger-ink disabled:opacity-30"
                            :disabled="rows.length === 1" @click="remove(index)" aria-label="Hapus anggota">
                            <x-heroicon-o-x-mark class="size-4" />
                        </button>
                    </div>
                </div>

                <template x-if="row.type === 'internal'">
                    <div>
                        <select x-init="initSelect($el, row)" aria-label="Pilih dosen"></select>
                        <input type="hidden" :name="`contributors[${index}][user_id]`" :value="row.user_id">
                    </div>
                </template>

                <template x-if="row.type === 'external'">
                    <div class="grid gap-2 sm:grid-cols-2">
                        <input type="text" :name="`contributors[${index}][name]`" x-model="row.name" maxlength="150"
                            placeholder="Nama lengkap" aria-label="Nama anggota luar PMC"
                            class="block w-full rounded-lg border border-line bg-surface px-3 py-2 text-sm shadow-xs focus:border-brand-200 focus:outline-none focus:ring-4 focus:ring-brand-600/10">
                        <input type="text" :name="`contributors[${index}][affiliation]`" x-model="row.affiliation" maxlength="150"
                            placeholder="Afiliasi / instansi" aria-label="Afiliasi"
                            class="block w-full rounded-lg border border-line bg-surface px-3 py-2 text-sm shadow-xs focus:border-brand-200 focus:outline-none focus:ring-4 focus:ring-brand-600/10">
                    </div>
                </template>
            </li>
        </template>
    </ol>

    <x-ui.button type="button" variant="outline" size="sm" icon="heroicon-o-plus" x-on:click="add()">Tambah anggota</x-ui.button>
</fieldset>
