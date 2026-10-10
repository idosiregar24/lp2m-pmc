<?php

namespace App\Http\Requests\Publication;

use App\Enums\PublicationLevel;
use App\Enums\VenueType;
use App\Http\Requests\Concerns\ValidatesContributors;
use App\Http\Requests\Concerns\ValidatesDocuments;
use App\Models\Publication;
use App\Rules\ValidYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicationRequest extends FormRequest
{
    use ValidatesContributors, ValidatesDocuments;

    public function authorize(): bool
    {
        return $this->user()->can('create', Publication::class);
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
            'venue_type' => ['required', Rule::enum(VenueType::class)],
            'venue_name' => ['required', 'string', 'max:255'],
            'level' => ['required', Rule::enum(PublicationLevel::class)],
            'publisher' => ['required', 'string', 'max:255'],
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
        return [
            'title' => 'judul publikasi',
            'year' => 'tahun terbit',
            'venue_type' => 'jenis wadah',
            'venue_name' => VenueType::tryFrom((string) $this->input('venue_type'))?->venueLabel() ?? 'nama jurnal/prosiding',
            'level' => 'level',
            'publisher' => 'penerbit',
        ];
    }

    protected function existingDocumentCount(): int
    {
        return 0;
    }
}
