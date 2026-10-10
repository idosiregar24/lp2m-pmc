<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\Concerns\ValidatesContributors;
use App\Http\Requests\Concerns\ValidatesDocuments;
use App\Models\Book;
use App\Rules\ValidYear;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    use ValidatesContributors, ValidatesDocuments;

    public function authorize(): bool
    {
        return $this->user()->can('create', Book::class);
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
        return ['title' => 'judul buku', 'year' => 'tahun terbit', 'publisher' => 'penerbit'];
    }

    protected function existingDocumentCount(): int
    {
        return 0;
    }
}
