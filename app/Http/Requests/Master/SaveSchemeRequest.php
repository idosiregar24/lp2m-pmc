<?php

namespace App\Http\Requests\Master;

use App\Enums\SchemeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSchemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(SchemeType::class)],
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('schemes', 'name')->where('type', (string) $this->input('type'))->ignore($this->route('scheme')),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['name' => 'nama skema', 'type' => 'jenis', 'is_active' => 'status'];
    }
}
