{{-- Shared by create & edit. Expects $publication, $years, $lecturers, $contributorRows, module options, $action, $method. --}}
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

    @if ($publication->exists && $publication->status === \App\Enums\VerificationStatus::NeedsRevision && $publication->revision_note)
        <x-ui.alert tone="warning" title="Catatan revisi dari admin">
            <p class="whitespace-pre-line">{{ $publication->revision_note }}</p>
        </x-ui.alert>
    @endif

    <x-ui.card title="Informasi Utama">
        <div class="grid gap-5 md:grid-cols-2">
            <x-form.field name="title" label="Judul Publikasi" required class="md:col-span-2">
                <x-form.textarea name="title" rows="2" :value="$publication->title" maxlength="500" required />
            </x-form.field>

            <x-form.field name="year" label="Tahun Terbit" required>
                <x-form.select name="year" :options="$years" :value="$publication->year" placeholder="Pilih tahun" required />
            </x-form.field>

            <div class="grid gap-5 md:col-span-2 md:grid-cols-2"
                x-data="{ venueType: @js(old('venue_type', $publication->venue_type?->value)) }">
                <x-form.field name="venue_type" label="Jenis Wadah" required>
                    <x-form.select name="venue_type" :options="$venueTypes" :value="$publication->venue_type?->value" placeholder="Pilih jenis" required x-model="venueType" />
                </x-form.field>

                <x-form.field name="level" label="Level" required>
                    <x-form.select name="level" :options="$levels" :value="$publication->level?->value" placeholder="Pilih level" required />
                </x-form.field>

                <x-form.field name="venue_name" required class="md:col-span-2">
                    <x-slot:label>
                        <span x-text="venueType === 'PROCEEDING' ? 'Nama Prosiding' : (venueType === 'JOURNAL' ? 'Nama Jurnal' : 'Nama Jurnal / Prosiding')">Nama Jurnal / Prosiding</span>
                    </x-slot:label>
                    <x-form.input name="venue_name" :value="$publication->venue_name" maxlength="255" required />
                </x-form.field>
            </div>

            <x-form.field name="publisher" label="Penerbit" required class="md:col-span-2">
                <x-form.input name="publisher" :value="$publication->publisher" maxlength="255" required />
            </x-form.field>
        </div>
    </x-ui.card>

    <x-ui.card title="Penulis" description="Urutkan sesuai urutan penulis di artikel.">
        <x-shared.contributors-field label="Penulis" :lecturers="$lecturers" :value="$contributorRows" lead-label="Penulis pertama" />
    </x-ui.card>

    <x-ui.card title="Dokumen Pendukung" description="Mis. file artikel, halaman sampul & daftar isi, atau LoA.">
        <x-shared.documents-field :existing="$publication->exists ? $publication->documents->count() : 0" />
        @if (! $isAdmin && config('bp2m.verification.require_document'))
            <p class="mt-2 text-xs text-muted">Minimal 1 dokumen diperlukan untuk mengajukan verifikasi.</p>
        @endif
    </x-ui.card>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <x-ui.button variant="ghost" :href="$publication->exists ? route('publications.show', $publication) : route('publications.index')">Batal</x-ui.button>
        @if ($isAdmin)
            <x-ui.button icon="heroicon-o-check" x-bind:disabled="busy">Simpan</x-ui.button>
        @else
            <x-ui.button variant="secondary" x-on:click="$refs.intent.value = '0'" x-bind:disabled="busy">Simpan sebagai Draf</x-ui.button>
            <x-ui.button x-on:click="$refs.intent.value = '1'" icon="heroicon-o-paper-airplane" x-bind:disabled="busy">Simpan &amp; Ajukan</x-ui.button>
        @endif
    </div>
</form>
