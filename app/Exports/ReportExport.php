<?php

namespace App\Exports;

use App\Models\User;
use App\Support\Verifiable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Rekap per dosen — rows from ReportService::rows() (VERIFIED records only).
 */
class ReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle
{
    /**
     * @param  Collection<int, array{user: User, counts: array<string, int>, total: int}>  $rows
     */
    public function __construct(
        private readonly Collection $rows,
        private readonly ?string $year,
    ) {}

    public function collection(): Collection
    {
        return $this->rows->map(fn (array $row) => array_map(ActivityExport::safe(...), [
            $row['user']->name,
            $row['user']->nidn_nuptk,
            $row['user']->studyProgram?->faculty?->name,
            $row['user']->studyProgram?->displayName(),
            ...array_values($row['counts']),
            $row['total'],
        ]));
    }

    public function headings(): array
    {
        return [
            'Nama Dosen', 'NIDN/NUPTK', 'Fakultas', 'Prodi',
            ...array_column(Verifiable::MODULES, 'label'),
            'Total',
        ];
    }

    public function title(): string
    {
        return $this->year ? "Rekap {$this->year}" : 'Rekap Semua Tahun';
    }
}
