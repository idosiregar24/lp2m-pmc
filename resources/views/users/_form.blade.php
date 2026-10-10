{{-- Shared by create & edit. Expects $user, $roles, $faculties, $programs, $action, $method. --}}
@php
    $isEdit = $user->exists;
    $isSelf = $isEdit && $user->is(auth()->user());
    $role = old('role', $user->role?->value);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6" x-data="{ busy: false, role: @js($role) }" @submit="busy = true">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <x-ui.alert tone="danger">Periksa kembali isian yang ditandai.</x-ui.alert>
    @endif

    <x-ui.card title="Data Diri">
        <div class="grid gap-5 md:grid-cols-2">
            <x-form.field name="name" label="Nama lengkap" hint="Termasuk gelar, mis. Dr. Budi Santoso, M.Kep." required class="md:col-span-2">
                <x-form.input name="name" :value="$user->name" maxlength="150" required />
            </x-form.field>

            <x-form.field name="role" label="Role" required>
                <x-form.select name="role" :options="$roles" :value="$user->role" x-model="role" required :disabled="$isSelf" />
                @if ($isSelf)
                    <input type="hidden" name="role" value="{{ $user->role->value }}">
                    <p class="text-xs text-muted">Role akun Anda sendiri tidak dapat diubah.</p>
                @endif
            </x-form.field>

            <div class="space-y-1.5">
                <x-form.label for="nidn_nuptk">
                    NIDN/NUPTK
                    <span x-show="role === 'DOSEN'" class="text-danger-ink" aria-hidden="true">*</span>
                </x-form.label>
                <x-form.input name="nidn_nuptk" :value="$user->nidn_nuptk" inputmode="numeric" maxlength="16" x-bind:required="role === 'DOSEN'" />
                <p class="text-xs text-muted">10 digit (NIDN/NIDK) atau 16 digit (NUPTK).</p>
                <x-form.error name="nidn_nuptk" />
            </div>

            <div class="contents" x-data="facultyStudyProgram({
                faculty: @js(old('faculty_id', $user->studyProgram?->faculty_id)),
                studyProgram: @js(old('study_program_id', $user->study_program_id)),
                programs: @js($programs),
            })">
                <div class="space-y-1.5">
                    <x-form.label for="faculty_id">
                        Fakultas <span x-show="role === 'DOSEN'" class="text-danger-ink" aria-hidden="true">*</span>
                    </x-form.label>
                    <x-form.select name="faculty_id" :options="$faculties" placeholder="Pilih fakultas" x-model="faculty" x-bind:required="role === 'DOSEN'" />
                    <x-form.error name="faculty_id" />
                </div>
                <div class="space-y-1.5">
                    <x-form.label for="study_program_id">
                        Prodi <span x-show="role === 'DOSEN'" class="text-danger-ink" aria-hidden="true">*</span>
                    </x-form.label>
                    <select name="study_program_id" id="study_program_id" x-model="studyProgram" x-bind:required="role === 'DOSEN'" :disabled="! faculty"
                        class="block w-full rounded-lg border border-line bg-surface px-3 py-2 text-sm text-ink shadow-xs focus:border-brand-200 focus:outline-none focus:ring-4 focus:ring-brand-600/10 disabled:bg-canvas disabled:text-muted">
                        <option value="" x-text="faculty ? 'Pilih prodi' : 'Pilih fakultas dulu'"></option>
                        <template x-for="program in filteredPrograms" :key="program.id">
                            <option :value="program.id" x-text="program.label" :selected="program.id === studyProgram"></option>
                        </template>
                    </select>
                    <x-form.error name="study_program_id" />
                </div>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card title="Akun">
        <div class="grid gap-5 md:grid-cols-2">
            <x-form.field name="username" label="Username" hint="4–30 karakter: huruf kecil, angka, titik, garis bawah; diawali huruf." required>
                <x-form.input name="username" :value="$user->username" maxlength="30" autocapitalize="none" spellcheck="false" required />
            </x-form.field>

            <div class="flex items-end pb-1">
                @if ($isSelf)
                    <input type="hidden" name="is_active" value="1">
                @else
                    <x-form.checkbox name="is_active" label="Akun aktif" :checked="$user->is_active ?? true" />
                @endif
            </div>

            <x-form.field name="password" :label="$isEdit ? 'Password baru' : 'Password'" :required="! $isEdit"
                :hint="$isEdit ? 'Kosongkan bila tidak diubah. Password baru bersifat sementara.' : 'Minimal 8 karakter. Pengguna wajib menggantinya saat pertama masuk.'">
                <x-form.input name="password" type="password" autocomplete="new-password" minlength="8" :required="! $isEdit" />
            </x-form.field>

            <x-form.field name="password_confirmation" label="Ulangi password" :required="! $isEdit">
                <x-form.input name="password_confirmation" type="password" autocomplete="new-password" :required="! $isEdit" />
            </x-form.field>
        </div>
    </x-ui.card>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <x-ui.button variant="ghost" :href="$isEdit ? route('users.show', $user) : route('users.index')">Batal</x-ui.button>
        <x-ui.button icon="heroicon-o-check" x-bind:disabled="busy">Simpan</x-ui.button>
    </div>
</form>
