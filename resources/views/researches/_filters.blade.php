<x-shared.filter-bar :action="route('researches.index')" :lecturer="$lecturer">
    <x-slot:search>
        <x-form.label for="q" class="sr-only">Cari</x-form.label>
        <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari judul atau nama anggota…" />
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
        <x-form.label for="scheme" class="sr-only">Skema</x-form.label>
        <x-form.select name="scheme" :options="$schemes" :value="$filters['scheme'] ?? null" placeholder="Semua skema" />
    </div>
    <div>
        <x-form.label for="funding_source" class="sr-only">Sumber dana</x-form.label>
        <x-form.select name="funding_source" :options="$fundingSources" :value="$filters['funding_source'] ?? null" placeholder="Semua sumber dana" />
    </div>
    <div>
        <x-form.label for="status" class="sr-only">Status</x-form.label>
        <x-form.select name="status" :options="$statuses" :value="$filters['status'] ?? null" placeholder="Semua status" />
    </div>
</x-shared.filter-bar>
