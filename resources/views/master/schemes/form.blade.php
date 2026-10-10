<x-layouts.app :title="$scheme->exists ? 'Ubah Skema' : 'Tambah Skema'"
    :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Skema', 'url' => route('master.schemes.index')], ['label' => $scheme->exists ? 'Ubah' : 'Tambah']]">
    <x-shared.form-card :title="$scheme->exists ? 'Ubah Skema' : 'Tambah Skema'"
        :action="$scheme->exists ? route('master.schemes.update', $scheme) : route('master.schemes.store')"
        :method="$scheme->exists ? 'PUT' : 'POST'" :cancel="route('master.schemes.index')">
        <x-form.field name="type" label="Jenis" hint="Skema Penelitian hanya muncul di form Penelitian, skema Pengabdian di form Pengabdian." required>
            <x-form.select name="type" :options="$types" :value="$scheme->type" placeholder="Pilih jenis" required />
        </x-form.field>
        <x-form.field name="name" label="Nama skema" required>
            <x-form.input name="name" :value="$scheme->name" maxlength="150" placeholder="mis. Penelitian Dosen Pemula" required />
        </x-form.field>
        <x-form.checkbox name="is_active" label="Aktif (muncul di pilihan form)" :checked="$scheme->is_active" />
    </x-shared.form-card>
</x-layouts.app>
