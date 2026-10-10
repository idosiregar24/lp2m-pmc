<x-layouts.app :title="$faculty->exists ? 'Ubah Fakultas' : 'Tambah Fakultas'"
    :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Fakultas', 'url' => route('master.faculties.index')], ['label' => $faculty->exists ? 'Ubah' : 'Tambah']]">
    <x-shared.form-card :title="$faculty->exists ? 'Ubah Fakultas' : 'Tambah Fakultas'"
        :action="$faculty->exists ? route('master.faculties.update', $faculty) : route('master.faculties.store')"
        :method="$faculty->exists ? 'PUT' : 'POST'" :cancel="route('master.faculties.index')">
        <x-form.field name="name" label="Nama fakultas" required>
            <x-form.input name="name" :value="$faculty->name" maxlength="150" required />
        </x-form.field>
        <x-form.field name="code" label="Kode" hint="Singkatan, mis. FIKES.">
            <x-form.input name="code" :value="$faculty->code" maxlength="20" />
        </x-form.field>
        <x-form.checkbox name="is_active" label="Aktif (muncul di pilihan form)" :checked="$faculty->is_active" />
    </x-shared.form-card>
</x-layouts.app>
