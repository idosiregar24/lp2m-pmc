<?php

namespace App\Http\Requests\IntellectualProperty;

use App\Enums\IntellectualPropertyType;
use App\Http\Requests\Concerns\ValidatesContributors;
use App\Http\Requests\Concerns\ValidatesDocuments;
use App\Models\IntellectualProperty;
use App\Rules\ValidYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIntellectualPropertyRequest extends FormRequest
{
    use ValidatesContributors, ValidatesDocuments;

    public function authorize(): bool
    {
        return $this->user()->can('create', IntellectualProperty::class);
    }

    protected function prepareForValidation(): void
    {
        $this->prepareContributors();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'year' => ['required', 'integer', new ValidYear],
            'type' => ['required', Rule::enum(IntellectualPropertyType::class)],
            ...$this->contributorRules(),
            ...$this->documentRules($this->existingDocumentCount()),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->contributorMessages(),
            ...$this->documentMessages($this->existingDocumentCount()),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['title' => 'judul KI', 'year' => 'tahun terbit', 'type' => 'jenis KI'];
    }

    protected function existingDocumentCount(): int
    {
        return 0;
    }
}
