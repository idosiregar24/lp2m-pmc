<?php

namespace App\Http\Requests\Master;

use App\Enums\StudyProgramLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStudyProgramRequest extends FormRequest
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
            'faculty_id' => ['required', 'integer', 'exists:faculties,id'],
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('study_programs', 'name')
                    ->where('faculty_id', (int) $this->input('faculty_id'))
                    ->where('level', (string) $this->input('level'))
                    ->ignore($this->route('studyProgram')),
            ],
            'level' => ['required', Rule::enum(StudyProgramLevel::class)],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['name' => 'nama prodi', 'is_active' => 'status'];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['name.unique' => 'Prodi dengan nama dan jenjang ini sudah ada di fakultas tersebut.'];
    }
}
