<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

/**
 * documents[] — PDFs, size and count limits from config/bp2m.php (PRD §6.2).
 * "Simpan & Ajukan" needs at least one document overall [ASUMSI PRD §4.11].
 */
trait ValidatesDocuments
{
    /**
     * @param  int  $existing  documents already attached to the record
     * @return array<string, mixed>
     */
    protected function documentRules(int $existing = 0): array
    {
        $remaining = max(0, (int) config('bp2m.documents.max_files') - $existing);

        return [
            'documents' => [
                Rule::requiredIf(fn () => $this->boolean('submit')
                    && config('bp2m.verification.require_document')
                    && $existing === 0),
                'array',
                'max:'.$remaining,
            ],
            'documents.*' => [
                'file',
                'mimes:'.implode(',', config('bp2m.documents.mimes')),
                'mimetypes:application/pdf',
                'max:'.config('bp2m.documents.max_kb'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function documentMessages(int $existing = 0): array
    {
        $max = (int) config('bp2m.documents.max_files');

        return [
            'documents.required' => 'Lampirkan minimal 1 dokumen pendukung sebelum mengajukan verifikasi.',
            'documents.max' => "Maksimal {$max} dokumen per data".($existing ? " (sudah ada {$existing})." : '.'),
            'documents.*.mimes' => 'Dokumen pendukung harus berupa file PDF.',
            'documents.*.mimetypes' => 'Dokumen pendukung harus berupa file PDF.',
            'documents.*.max' => 'Ukuran dokumen maksimal '.((int) config('bp2m.documents.max_kb') / 1024).' MB per file.',
        ];
    }
}
