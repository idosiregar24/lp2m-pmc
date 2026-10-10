<x-shared.filter-bar :action="route('publications.index')" :lecturer="$lecturer">
    <x-slot:search>
        <x-form.label for="q" class="sr-only">Cari</x-form.label>
        <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari judul, jurnal/prosiding, penerbit, atau penulis…" />
    </x-slot:search>
    <div>
        <x-form.label for="year" class="sr-only">Tahun</x-form.label>
        <x-form.select name="year" :options="$years" :value="$filters['year'] ?? null" placeholder="Semua tahun" />
    </div>
    <div>
        <x-form.label for="faculty" class="sr-only">Fakultas</x-form.label>
        <x-form.select name="faculty" :options="$faculties" :value="$filters['faculty'] ?? null" placeholder="Semua fakultas" />
    </div>
    <div>
        <x-form.label for="study_program" class="sr-only">Prodi</x-form.label>
        <x-form.select name="study_program" :options="$studyPrograms" :value="$filters['study_program'] ?? null" placeholder="Semua prodi" />
    </div>
    <div>
        <x-form.label for="venue_type" class="sr-only">Jenis wadah</x-form.label>
        <x-form.select name="venue_type" :options="$venueTypes" :value="$filters['venue_type'] ?? null" placeholder="Jurnal & prosiding" />
    </div>
    <div>
        <x-form.label for="level" class="sr-only">Level</x-form.label>
        <x-form.select name="level" :options="$levels" :value="$filters['level'] ?? null" placeholder="Semua level" />
    </div>
    <div>
        <x-form.label for="status" class="sr-only">Status</x-form.label>
        <x-form.select name="status" :options="$statuses" :value="$filters['status'] ?? null" placeholder="Semua status" />
    </div>
</x-shared.filter-bar>
