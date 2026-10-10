<?php

namespace App\Http\Requests\Concerns;

use App\Enums\UserRole;
use Illuminate\Validation\Rule;

/**
 * contributors[i][user_id] (internal lecturer) or contributors[i][name] (+ affiliation)
 * for an outside person. ContributorService enforces the remaining rules.
 */
trait ValidatesContributors
{
    /**
     * @return array<string, mixed>
     */
    protected function contributorRules(): array
    {
        return [
            'contributors' => ['required', 'array', 'min:1', 'max:50'],
            'contributors.*' => ['array'],
            'contributors.*.user_id' => ['nullable', 'integer', 'distinct', Rule::exists('users', 'id')->where(fn ($q) => $q
                ->where('role', UserRole::Dosen->value)
                ->when($this->currentContributorIds(), fn ($q, $ids) => $q->orWhereIn('id', $ids)))],
            'contributors.*.name' => ['nullable', 'required_without:contributors.*.user_id', 'string', 'max:150'],
            'contributors.*.affiliation' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * Users already on the record stay valid after a role change (e.g. promoted to Admin).
     *
     * @return array<int, int>
     */
    protected function currentContributorIds(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    protected function contributorMessages(): array
    {
        return [
            'contributors.required' => 'Minimal satu anggota wajib diisi.',
            'contributors.*.user_id.distinct' => 'Dosen yang sama tidak boleh dicantumkan dua kali.',
            'contributors.*.user_id.exists' => 'Dosen yang dipilih tidak valid.',
            'contributors.*.name.required_without' => 'Pilih dosen atau isi nama anggota dari luar PMC.',
        ];
    }

    /**
     * Rows the form left completely empty are not sent to validation.
     */
    protected function prepareContributors(): void
    {
        if (! is_array($this->input('contributors'))) {
            return;
        }

        $rows = array_values(array_filter(
            $this->input('contributors'),
            fn ($row) => is_array($row) && (filled($row['user_id'] ?? null) || filled($row['name'] ?? null)),
        ));

        $this->merge(['contributors' => $rows]);
    }
}
