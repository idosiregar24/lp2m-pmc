<?php

namespace App\Exports;

use App\Models\Contributor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Excel export of one research-data module (PRD §4.10). The query arrives already
 * scoped by visibleTo() + the list filters; this class only shapes the columns:
 * Judul, Tahun, module columns, Anggota, Fakultas/Prodi of the first member,
 * Jumlah Dokumen, Status. No file paths are exported.
 */
abstract class ActivityExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Builder $query) {}

    /**
     * @return list<string>
     */
    abstract protected function moduleHeadings(): array;

    /**
     * @return list<mixed>
     */
    abstract protected function moduleValues(Model $record): array;

    /**
     * Module relations the columns read.
     *
     * @return list<string>
     */
    protected function moduleRelations(): array
    {
        return [];
    }

    public function query(): Builder
    {
        return $this->query
            ->with([...$this->moduleRelations(), 'contributors.user.studyProgram.faculty'])
            ->withCount('documents');
    }

    public function headings(): array
    {
        return [
            'Judul', 'Tahun', ...$this->moduleHeadings(),
            'Anggota', 'Fakultas (Anggota Pertama)', 'Prodi (Anggota Pertama)', 'Jumlah Dokumen', 'Status',
        ];
    }

    /**
     * @return list<mixed>
     */
    public function map($record): array
    {
        $lead = $record->contributors->first();

        return array_map(self::safe(...), [
            $record->title,
            $record->year,
            ...$this->moduleValues($record),
            $record->contributors->map(fn (Contributor $contributor) => $contributor->displayName()
                .($contributor->isExternal() && $contributor->affiliation ? " ({$contributor->affiliation})" : ''))
                ->implode('; '),
            $lead?->user?->studyProgram?->faculty?->name,
            $lead?->user?->studyProgram?->displayName(),
            $record->documents_count,
            $record->status->label(),
        ]);
    }

    /**
     * A text cell starting with = + - @ (or a control character) would run as a
     * formula when opened in Excel — prefix it with an apostrophe (security-standards §5).
     */
    public static function safe(mixed $value): mixed
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
