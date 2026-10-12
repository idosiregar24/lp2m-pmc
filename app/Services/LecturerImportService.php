<?php

namespace App\Services;

use App\Enums\StudyProgramLevel;
use App\Enums\UserRole;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

class LecturerImportService
{
    /**
     * Import lecturers from spreadsheet file (XLSX, XLS, CSV).
     *
     * Expected columns (in header row or cols A-E):
     * A: Fakultas
     * B: Prodi
     * C: Nama Dosen
     * D: NIDN
     * E: NUPTK
     *
     * @return array{
     *     total: int,
     *     imported: int,
     *     updated: int,
     *     skipped: int,
     *     errors: list<string>
     * }
     */
    public function import(UploadedFile|string $file, bool $updateExisting = true): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! file_exists($filePath)) {
            throw new RuntimeException("File tidak ditemukan: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return [
                'total' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => ['File spreadsheet kosong.'],
            ];
        }

        // Find header row and column mapping
        $headerInfo = $this->resolveColumns($rows);
        $startIndex = $headerInfo['header_row_index'] + 1;
        $colMap = $headerInfo['columns'];

        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            for ($i = $startIndex; $i < count($rows); $i++) {
                $row = $rows[$i];

                $nama = trim((string) ($row[$colMap['nama']] ?? ''));
                if ($nama === '' || $nama === '-') {
                    $skippedCount++;
                    continue;
                }

                $rawFakultas = trim((string) ($row[$colMap['fakultas']] ?? ''));
                $rawProdi = trim((string) ($row[$colMap['prodi']] ?? ''));
                $rawNidn = trim((string) ($row[$colMap['nidn']] ?? ''));
                $rawNuptk = trim((string) ($row[$colMap['nuptk']] ?? ''));

                // Clean NIDN / NUPTK
                $nidnDigits = preg_replace('/\D/', '', $rawNidn);
                $nuptkDigits = preg_replace('/\D/', '', $rawNuptk);

                // Priority: NIDN (10 digits) if available, otherwise NUPTK (16 digits), or any digits
                $nidnNuptk = null;
                if (! empty($nidnDigits) && strlen($nidnDigits) >= 8) {
                    $nidnNuptk = substr($nidnDigits, 0, 16);
                } elseif (! empty($nuptkDigits) && strlen($nuptkDigits) >= 8) {
                    $nidnNuptk = substr($nuptkDigits, 0, 16);
                } elseif (! empty($nidnDigits)) {
                    $nidnNuptk = substr($nidnDigits, 0, 16);
                }

                // Resolve Faculty & Study Program
                $faculty = $this->resolveFaculty($rawFakultas);
                $studyProgram = $this->resolveStudyProgram($faculty, $rawProdi);

                // Check existing lecturer
                $existingUser = null;
                if ($nidnNuptk !== null) {
                    $existingUser = User::query()->where('nidn_nuptk', $nidnNuptk)->first();
                }

                if (! $existingUser) {
                    $existingUser = User::query()
                        ->where('role', UserRole::Dosen)
                        ->where('name', $nama)
                        ->where('study_program_id', $studyProgram->id)
                        ->first();
                }

                if ($existingUser) {
                    if ($updateExisting) {
                        $existingUser->name = $nama;
                        $existingUser->study_program_id = $studyProgram->id;
                        if ($nidnNuptk !== null && empty($existingUser->nidn_nuptk)) {
                            $existingUser->nidn_nuptk = $nidnNuptk;
                        }
                        $existingUser->is_active = true;
                        $existingUser->save();
                        $updatedCount++;
                    } else {
                        $skippedCount++;
                    }
                } else {
                    $username = $this->generateUniqueUsername($nama, $nidnNuptk);

                    User::create([
                        'name' => $nama,
                        'nidn_nuptk' => $nidnNuptk,
                        'study_program_id' => $studyProgram->id,
                        'username' => $username,
                        'password' => Hash::make('pmc12345'),
                        'role' => UserRole::Dosen,
                        'is_active' => true,
                        'must_change_password' => true,
                    ]);

                    $createdCount++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'total' => $createdCount + $updatedCount + $skippedCount,
            'imported' => $createdCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Resolve header row and columns index mapping.
     *
     * @param  array<int, array<int, mixed>>  $rows
     * @return array{header_row_index: int, columns: array{fakultas: int, prodi: int, nama: int, nidn: int, nuptk: int}}
     */
    private function resolveColumns(array $rows): array
    {
        // Default column positions if headers are not explicitly labeled
        $defaultMap = [
            'fakultas' => 0,
            'prodi' => 1,
            'nama' => 2,
            'nidn' => 3,
            'nuptk' => 4,
        ];

        // Search the first 5 rows for header keywords
        for ($r = 0; $r < min(5, count($rows)); $r++) {
            $row = $rows[$r];
            $map = [];

            foreach ($row as $colIdx => $val) {
                $text = strtolower(trim((string) $val));

                if (str_contains($text, 'fakultas')) {
                    $map['fakultas'] = $colIdx;
                } elseif (str_contains($text, 'prodi') || str_contains($text, 'program studi')) {
                    $map['prodi'] = $colIdx;
                } elseif (str_contains($text, 'nama')) {
                    $map['nama'] = $colIdx;
                } elseif (str_contains($text, 'nidn')) {
                    $map['nidn'] = $colIdx;
                } elseif (str_contains($text, 'nuptk')) {
                    $map['nuptk'] = $colIdx;
                }
            }

            if (isset($map['nama'])) {
                return [
                    'header_row_index' => $r,
                    'columns' => array_merge($defaultMap, $map),
                ];
            }
        }

        return [
            'header_row_index' => 0,
            'columns' => $defaultMap,
        ];
    }

    /**
     * Find or create Faculty.
     */
    private function resolveFaculty(string $rawFaculty): Faculty
    {
        $raw = trim($rawFaculty);
        if ($raw === '') {
            $raw = 'Kesehatan';
        }

        // Try exact match
        $found = Faculty::query()
            ->where('name', $raw)
            ->orWhere('name', 'Fakultas '.$raw)
            ->first();

        if ($found) {
            return $found;
        }

        // Try fuzzy match
        $found = Faculty::query()
            ->where('name', 'like', '%'.$raw.'%')
            ->first();

        if ($found) {
            return $found;
        }

        $canonicalName = str_starts_with(strtolower($raw), 'fakultas') ? $raw : 'Fakultas '.$raw;
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $raw), 0, 4)) ?: 'PMC';

        return Faculty::create([
            'name' => $canonicalName,
            'code' => $code,
            'is_active' => true,
        ]);
    }

    /**
     * Find or create StudyProgram.
     */
    private function resolveStudyProgram(Faculty $faculty, string $rawProdi): StudyProgram
    {
        $raw = trim($rawProdi);
        if ($raw === '') {
            $raw = 'Umum';
        }

        $lower = strtolower($raw);

        // Detect program level
        $level = StudyProgramLevel::S1;
        if (str_contains($lower, 'profesi')) {
            $level = StudyProgramLevel::Profesi;
        } elseif (str_contains($lower, 'd3') || str_contains($lower, 'diploma 3') || str_contains($lower, 'd-iii')) {
            $level = StudyProgramLevel::D3;
        } elseif (str_contains($lower, 'd4') || str_contains($lower, 'sarjana terapan') || str_contains($lower, 'd-iv')) {
            $level = StudyProgramLevel::D4;
        } elseif (str_contains($lower, 's2') || str_contains($lower, 'magister')) {
            $level = StudyProgramLevel::S2;
        } elseif (str_contains($lower, 's3') || str_contains($lower, 'doktor')) {
            $level = StudyProgramLevel::S3;
        }

        // Search in this faculty
        $found = StudyProgram::query()
            ->where('faculty_id', $faculty->id)
            ->where(function ($q) use ($raw) {
                $q->where('name', $raw)
                    ->orWhere('name', 'like', '%'.$raw.'%')
                    ->orWhereRaw('? like concat("%", name, "%")', [$raw]);
            })
            ->first();

        if ($found) {
            return $found;
        }

        return StudyProgram::create([
            'faculty_id' => $faculty->id,
            'name' => $raw,
            'level' => $level,
            'is_active' => true,
        ]);
    }

    /**
     * Generate unique single-word username for lecturer (satu kata saja).
     */
    private function generateUniqueUsername(string $name, ?string $nidn): string
    {
        // 1. Strip degrees after comma
        $beforeComma = trim(Str::before($name, ','));

        // 2. Tokenize words by whitespace
        $words = preg_split('/[\s]+/', $beforeComma, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        // Common title prefixes to strip
        $titlePrefixes = [
            'dr', 'drg', 'prof', 'ns', 'bdn', 'apt', 'ir', 'h', 'hj', 'drs', 'dra', 'kh', 'ust', 'ustadz', 'ustadzah',
        ];

        $validWords = [];
        foreach ($words as $w) {
            $cleanedWord = strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $w)));
            if ($cleanedWord === '') {
                continue;
            }
            // Skip title if it ends with dot (e.g. "Dr.", "Ns.") or matches known title prefixes
            if (in_array($cleanedWord, $titlePrefixes, true) || str_ends_with(trim($w), '.')) {
                continue;
            }
            $validWords[] = $cleanedWord;
        }

        // Pick the first clean word
        $firstWord = $validWords[0] ?? '';

        // If first word is empty, fallback to clean NIDN or 'dosen'
        if ($firstWord === '') {
            $firstWord = 'dosen';
        }

        // Ensure minimum 4 characters (system validation regex is ^[a-z][a-z0-9._]{3,29}$)
        // If shorter than 4 chars, merge with second word without spaces/dots (e.g. 'dwi' + 'putri' -> 'dwiputri')
        // otherwise append '1' (e.g. 'dwi' -> 'dwi1')
        $base = $firstWord;
        if (strlen($base) < 4) {
            if (isset($validWords[1]) && strlen($validWords[1]) >= 2) {
                $base .= $validWords[1];
            } else {
                $base .= '1';
            }
        }

        // Cut if too long (keep within 20 chars for single-word username)
        $base = substr($base, 0, 20);

        // Ensure starts with a letter
        if (! preg_match('/^[a-z]/', $base)) {
            $base = 'd'.$base;
        }

        $username = $base;
        $counter = 2;

        while (User::query()->where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generate downloadable Excel template for lecturer upload.
     */
    public function generateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Dosen');

        // Headers
        $headers = ['Fakultas', 'Prodi', 'Nama Dosen', 'NIDN', 'NUPTK'];
        $sheet->fromArray($headers, null, 'A1');

        // Sample data rows (matching Universitas PMC official faculty structure)
        $sampleData = [
            ['Kesehatan', 'Gizi', 'Anugerah Humairah, S.Gz, M.kes', '1019099006', '2251768669230350'],
            ['Kesehatan', 'Ilmu Keperawatan', 'Isna Ovari, S.Kp., M. Kep', '1007027001', '3539748649230130'],
            ['Kesehatan', 'Kebidanan', 'Bdn. Endah Purwani Sari, SST., M.Kes', '1027099001', '3259768669230290'],
            ['Kesehatan', 'Pendidikan Profesi Bidan', 'Sidri Rizqi Yuniati, SST, M.KM', '1020068708', '6952765666230310'],
            ['Kesehatan', 'Profesi Ners', 'Ns. Meyunda Alfriyani, S.Kep., M.Kep', '', '8752775676230200'],
            ['Ekonomi dan Bisnis', 'Bisnis Digital', 'Fadhly Alfallah, S.Kom., M.Kom', '', '7138779680130080'],
            ['Ekonomi dan Bisnis', 'Kewirausahaan', 'Arbianti, S.E., M.Ak', '', ''],
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Format header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '961A22'], // PMC Brand Crimson
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Auto-fit column widths
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format NIDN and NUPTK as text columns so leading zeroes or large numbers don't scientific notation
        $sheet->getStyle('D2:E100')->getNumberFormat()->setFormatCode('@');

        $tempPath = tempnam(sys_get_temp_dir(), 'pmc_lecturer_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Preview lecturers from spreadsheet file (XLSX, XLS, CSV).
     *
     * @return array{
     *     file_name: string,
     *     file_size: string,
     *     total_rows: int,
     *     header_row: int,
     *     columns: array<string, array{index: int, letter: string, name: string}>,
     *     sample_rows: list<array{row_num: int, fakultas: string, prodi: string, nama: string, nidn: string, nuptk: string, valid: bool}>,
     *     errors: list<string>
     * }
     */
    public function preview(UploadedFile|string $file, int $maxRows = 6): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $fileName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);
        $fileSize = $file instanceof UploadedFile ? $file->getSize() : (file_exists($filePath) ? (int) filesize($filePath) : 0);

        if (! file_exists($filePath)) {
            throw new RuntimeException("File tidak ditemukan: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return [
                'file_name' => $fileName,
                'file_size' => $this->formatFileSize($fileSize),
                'total_rows' => 0,
                'header_row' => 1,
                'columns' => [],
                'sample_rows' => [],
                'errors' => ['File spreadsheet kosong.'],
            ];
        }

        $headerInfo = $this->resolveColumns($rows);
        $headerRowIdx = $headerInfo['header_row_index'];
        $colMap = $headerInfo['columns'];

        $colLetter = fn (int $idx) => chr(65 + $idx);

        $columns = [
            'fakultas' => [
                'index' => $colMap['fakultas'],
                'letter' => $colLetter($colMap['fakultas']),
                'name' => 'Fakultas',
            ],
            'prodi' => [
                'index' => $colMap['prodi'],
                'letter' => $colLetter($colMap['prodi']),
                'name' => 'Program Studi',
            ],
            'nama' => [
                'index' => $colMap['nama'],
                'letter' => $colLetter($colMap['nama']),
                'name' => 'Nama Dosen',
            ],
            'nidn' => [
                'index' => $colMap['nidn'],
                'letter' => $colLetter($colMap['nidn']),
                'name' => 'NIDN',
            ],
            'nuptk' => [
                'index' => $colMap['nuptk'],
                'letter' => $colLetter($colMap['nuptk']),
                'name' => 'NUPTK',
            ],
        ];

        $sampleRows = [];
        $totalRows = 0;
        $errors = [];

        for ($i = $headerRowIdx + 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $nama = trim((string) ($row[$colMap['nama']] ?? ''));
            $fakultas = trim((string) ($row[$colMap['fakultas']] ?? ''));
            $prodi = trim((string) ($row[$colMap['prodi']] ?? ''));
            $nidn = trim((string) ($row[$colMap['nidn']] ?? ''));
            $nuptk = trim((string) ($row[$colMap['nuptk']] ?? ''));

            if ($nama === '' && $fakultas === '' && $prodi === '' && $nidn === '' && $nuptk === '') {
                continue;
            }

            $totalRows++;

            if (count($sampleRows) < $maxRows) {
                $sampleRows[] = [
                    'row_num' => $i + 1,
                    'fakultas' => $fakultas,
                    'prodi' => $prodi,
                    'nama' => $nama,
                    'nidn' => $nidn,
                    'nuptk' => $nuptk,
                    'valid' => ($nama !== '' && $nama !== '-'),
                ];
            }
        }

        if ($totalRows === 0) {
            $errors[] = 'Tidak ditemukan baris data di dalam spreadsheet.';
        }

        return [
            'file_name' => $fileName,
            'file_size' => $this->formatFileSize($fileSize),
            'total_rows' => $totalRows,
            'header_row' => $headerRowIdx + 1,
            'columns' => $columns,
            'sample_rows' => $sampleRows,
            'errors' => $errors,
        ];
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $power), 1) . ' ' . $units[$power];
    }
}
