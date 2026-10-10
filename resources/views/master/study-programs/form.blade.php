<x-layouts.app :title="$studyProgram->exists ? 'Ubah Prodi' : 'Tambah Prodi'"
    :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Prodi', 'url' => route('master.study-programs.index')], ['label' => $studyProgram->exists ? 'Ubah' : 'Tambah']]">
    <x-shared.form-card :title="$studyProgram->exists ? 'Ubah Prodi' : 'Tambah Prodi'"
        :action="$studyProgram->exists ? route('master.study-programs.update', $studyProgram) : route('master.study-programs.store')"
        :method="$studyProgram->exists ? 'PUT' : 'POST'" :cancel="route('master.study-programs.index')">
        <x-form.field name="faculty_id" label="Fakultas" required>
            <x-form.select name="faculty_id" :options="$faculties" :value="$studyProgram->faculty_id" placeholder="Pilih fakultas" required />
        </x-form.field>
        <div class="grid gap-5 sm:grid-cols-3">
            <x-form.field name="level" label="Jenjang" required>
                <x-form.select name="level" :options="$levels" :value="$studyProgram->level" placeholder="Pilih" required />
            </x-form.field>
            <x-form.field name="name" label="Nama prodi" required class="sm:col-span-2">
                <x-form.input name="name" :value="$studyProgram->name" maxlength="150" placeholder="mis. Keperawatan" required />
            </x-form.field>
        </div>
        <x-form.checkbox name="is_active" label="Aktif (muncul di pilihan form)" :checked="$studyProgram->is_active" />
    </x-shared.form-card>
</x-layouts.app>
