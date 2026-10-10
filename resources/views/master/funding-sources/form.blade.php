<x-layouts.app :title="$fundingSource->exists ? 'Ubah Sumber Dana' : 'Tambah Sumber Dana'"
    :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Sumber Dana', 'url' => route('master.funding-sources.index')], ['label' => $fundingSource->exists ? 'Ubah' : 'Tambah']]">
    <x-shared.form-card :title="$fundingSource->exists ? 'Ubah Sumber Dana' : 'Tambah Sumber Dana'"
        :action="$fundingSource->exists ? route('master.funding-sources.update', $fundingSource) : route('master.funding-sources.store')"
        :method="$fundingSource->exists ? 'PUT' : 'POST'" :cancel="route('master.funding-sources.index')">
        <x-form.field name="name" label="Nama sumber dana" required>
            <x-form.input name="name" :value="$fundingSource->name" maxlength="150" placeholder="mis. Hibah Kemdiktisaintek" required />
        </x-form.field>
        <x-form.checkbox name="is_active" label="Aktif (muncul di pilihan form)" :checked="$fundingSource->is_active" />
    </x-shared.form-card>
</x-layouts.app>
