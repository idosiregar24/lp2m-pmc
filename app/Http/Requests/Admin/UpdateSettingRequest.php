<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Logo upload (PRD §4.12). SVG is refused — it can carry scripts when opened directly.
 */
class UpdateSettingRequest extends FormRequest
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
        $min = (int) config('bp2m.logo.min_size');

        return [
            'remove_logo' => ['sometimes', 'boolean'],
            'logo' => [
                'required_unless:remove_logo,1',
                'nullable',
                'image',
                'mimes:'.implode(',', config('bp2m.logo.mimes')),
                'max:'.config('bp2m.logo.max_kb'),
                "dimensions:min_width={$min},min_height={$min}",
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logo.required_unless' => 'Pilih file logo yang akan diunggah.',
            'logo.mimes' => 'Logo harus berupa PNG, JPG, atau WebP.',
            'logo.max' => 'Ukuran logo maksimal 1 MB.',
            'logo.dimensions' => 'Logo minimal berukuran '.config('bp2m.logo.min_size').'×'.config('bp2m.logo.min_size').' piksel.',
        ];
    }
}
