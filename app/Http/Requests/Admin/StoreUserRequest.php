<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Account form (PRD §4.2). Faculty is only used to validate the prodi —
 * users store study_program_id alone.
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => mb_strtolower(trim((string) $this->input('username'))),
            'nidn_nuptk' => preg_replace('/\s+/', '', (string) $this->input('nidn_nuptk')) ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isDosen = $this->input('role') === UserRole::Dosen->value;
        $ignoreId = $this->targetUser()?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'nidn_nuptk' => [Rule::requiredIf($isDosen), 'nullable', 'regex:/^(\d{10}|\d{16})$/', Rule::unique('users', 'nidn_nuptk')->ignore($ignoreId)],
            'faculty_id' => [Rule::requiredIf($isDosen), 'nullable', 'integer', 'exists:faculties,id'],
            'study_program_id' => [
                Rule::requiredIf($isDosen), 'nullable', 'integer',
                Rule::exists('study_programs', 'id')->where('faculty_id', (int) $this->input('faculty_id')),
            ],
            'username' => ['required', 'string', 'regex:/^[a-z][a-z0-9._]{3,29}$/', Rule::unique('users', 'username')->ignore($ignoreId)],
            'password' => $this->passwordRules(),
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nidn_nuptk.regex' => 'NIDN/NUPTK harus 10 digit (NIDN/NIDK) atau 16 digit (NUPTK), angka saja.',
            'username.regex' => 'Username 4–30 karakter: huruf kecil, angka, titik, atau garis bawah, diawali huruf.',
            'study_program_id.exists' => 'Prodi harus berada di fakultas yang dipilih.',
            'nidn_nuptk.required' => 'NIDN/NUPTK wajib diisi untuk dosen.',
            'faculty_id.required' => 'Fakultas wajib diisi untuk dosen.',
            'study_program_id.required' => 'Prodi wajib diisi untuk dosen.',
        ];
    }

    /**
     * @return list<mixed>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::defaults(), 'confirmed'];
    }

    protected function targetUser(): ?User
    {
        return null;
    }
}
