<?php

namespace App\Http\Requests\CommunityService;

use App\Enums\SchemeType;
use App\Http\Requests\Concerns\ValidatesContributors;
use App\Http\Requests\Concerns\ValidatesDocuments;
use App\Http\Requests\Concerns\ValidatesFunding;
use App\Models\CommunityService;
use App\Rules\ValidYear;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommunityServiceRequest extends FormRequest
{
    use ValidatesContributors, ValidatesDocuments, ValidatesFunding;

    public function authorize(): bool
    {
        return $this->user()->can('create', CommunityService::class);
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
            'scheme_id' => ['required', 'integer', $this->schemeRule(SchemeType::CommunityService)],
            'partner' => ['required', 'string', 'max:255'],
            'funding_source_id' => ['required', 'integer', $this->fundingSourceRule()],
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
        return ['title' => 'judul pengabdian', 'partner' => 'mitra'];
    }

    protected function existingDocumentCount(): int
    {
        return 0;
    }
}
