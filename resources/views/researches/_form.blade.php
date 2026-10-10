{{-- Shared by create & edit. Expects $research, $years, $schemes, $fundingSources, $lecturers, $contributorRows, $action, $method. --}}
@php($isAdmin = auth()->user()->isAdmin())

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6"
    x-data="{ busy: false }" @submit="busy = true">
    @csrf
    {{-- Set by the clicked button: a disabled submit button would not send its own value. --}}
    <input type="hidden" name="submit" value="0" x-ref="intent">
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <x-ui.alert tone="danger">Periksa kembali isian yang ditandai.</x-ui.alert>
    @endif

    @if ($research->exists && $research->status === \App\Enums\VerificationStatus::NeedsRevision && $research->revision_note)
        <x-ui.alert tone="warning" title="Catatan revisi dari admin">
            <p class="whitespace-pre-line">{{ $research->revision_note }}</p>
        </x-ui.alert>
    @endif

    <x-ui.card title="Informasi Utama">
        <div class="grid gap-5 md:grid-cols-2">
            <x-form.field name="title" label="Judul Penelitian" required class="md:col-span-2">
                <x-form.textarea name="title" rows="2" :value="$research->title" maxlength="500" required />
            </x-form.field>

            <x-form.field name="year" label="Tahun" required>
                <x-form.select name="year" :options="$years" :value="$research->year" placeholder="Pilih tahun" required />
            </x-form.field>

            <x-form.field name="scheme_id" label="Jenis Skema" required>
                <x-form.select name="scheme_id" :options="$schemes" :value="$research->scheme_id" placeholder="Pilih skema" required />
            </x-form.field>

            <x-form.field name="funding_source_id" label="Sumber Dana" required>
                <x-form.select name="funding_source_id" :options="$fundingSources" :value="$research->funding_source_id" placeholder="Pilih sumber dana" required />
            </x-form.field>
        </div>
    </x-ui.card>

    <x-ui.card title="Peneliti" description="Ketua dan anggota penelitian.">
        <x-shared.contributors-field label="Peneliti" :lecturers="$lecturers" :value="$contributorRows" lead-label="Ketua" />
    </x-ui.card>

    <x-ui.card title="Dokumen Pendukung" description="Mis. SK, kontrak, laporan akhir, atau artikel hasil penelitian.">
        <x-shared.documents-field :existing="$research->exists ? $research->documents->count() : 0" />
        @if (! $isAdmin && config('bp2m.verification.require_document'))
            <p class="mt-2 text-xs text-muted">Minimal 1 dokumen diperlukan untuk mengajukan verifikasi.</p>
        @endif
    </x-ui.card>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <x-ui.button variant="ghost" :href="$research->exists ? route('researches.show', $research) : route('researches.index')">Batal</x-ui.button>
        @if ($isAdmin)
            <x-ui.button icon="heroicon-o-check" x-bind:disabled="busy">Simpan</x-ui.button>
        @else
            <x-ui.button variant="secondary" x-on:click="$refs.intent.value = '0'" x-bind:disabled="busy">Simpan sebagai Draf</x-ui.button>
            <x-ui.button x-on:click="$refs.intent.value = '1'" icon="heroicon-o-paper-airplane" x-bind:disabled="busy">Simpan &amp; Ajukan</x-ui.button>
        @endif
    </div>
</form>
