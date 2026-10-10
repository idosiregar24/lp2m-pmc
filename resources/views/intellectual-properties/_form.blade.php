{{-- Shared by create & edit. Expects $intellectualProperty, $years, $lecturers, $contributorRows, module options, $action, $method. --}}
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

    @if ($intellectualProperty->exists && $intellectualProperty->status === \App\Enums\VerificationStatus::NeedsRevision && $intellectualProperty->revision_note)
        <x-ui.alert tone="warning" title="Catatan revisi dari admin">
            <p class="whitespace-pre-line">{{ $intellectualProperty->revision_note }}</p>
        </x-ui.alert>
    @endif

    <x-ui.card title="Informasi Utama">
        <div class="grid gap-5 md:grid-cols-2">
            <x-form.field name="title" label="Judul KI" required class="md:col-span-2">
                <x-form.textarea name="title" rows="2" :value="$intellectualProperty->title" maxlength="500" required />
            </x-form.field>

            <x-form.field name="year" label="Tahun Terbit" required>
                <x-form.select name="year" :options="$years" :value="$intellectualProperty->year" placeholder="Pilih tahun" required />
            </x-form.field>

            <x-form.field name="type" label="Jenis KI" required>
                <x-form.select name="type" :options="$types" :value="$intellectualProperty->type?->value" placeholder="Pilih jenis KI" required />
            </x-form.field>
        </div>
    </x-ui.card>

    <x-ui.card title="Pemilik KI" description="Dosen PMC dan/atau pihak luar — boleh berupa institusi, mis. Universitas PMC.">
        <x-shared.contributors-field label="Pemilik KI" :lecturers="$lecturers" :value="$contributorRows" lead-label="Pemilik pertama" />
    </x-ui.card>

    <x-ui.card title="Dokumen Pendukung" description="Mis. sertifikat atau surat pencatatan KI.">
        <x-shared.documents-field :existing="$intellectualProperty->exists ? $intellectualProperty->documents->count() : 0" />
        @if (! $isAdmin && config('bp2m.verification.require_document'))
            <p class="mt-2 text-xs text-muted">Minimal 1 dokumen diperlukan untuk mengajukan verifikasi.</p>
        @endif
    </x-ui.card>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <x-ui.button variant="ghost" :href="$intellectualProperty->exists ? route('intellectual-properties.show', $intellectualProperty) : route('intellectual-properties.index')">Batal</x-ui.button>
        @if ($isAdmin)
            <x-ui.button icon="heroicon-o-check" x-bind:disabled="busy">Simpan</x-ui.button>
        @else
            <x-ui.button variant="secondary" x-on:click="$refs.intent.value = '0'" x-bind:disabled="busy">Simpan sebagai Draf</x-ui.button>
            <x-ui.button x-on:click="$refs.intent.value = '1'" icon="heroicon-o-paper-airplane" x-bind:disabled="busy">Simpan &amp; Ajukan</x-ui.button>
        @endif
    </div>
</form>
