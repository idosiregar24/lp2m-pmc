<?php

namespace App\Console\Commands;

use App\Services\LecturerImportService;
use Illuminate\Console\Command;

class ImportLecturersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lppm:import-lecturers {file : Path to the Excel/CSV file} {--no-update : Do not update existing lecturers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import lecturer accounts from an Excel or CSV spreadsheet';

    /**
     * Execute the console command.
     */
    public function handle(LecturerImportService $service): int
    {
        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");

            return self::FAILURE;
        }

        $this->info("Memproses file: {$filePath}...");

        $updateExisting = ! $this->option('no-update');
        $result = $service->import($filePath, $updateExisting);

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Baris Diproses', $result['total']],
                ['Dosen Baru Dibuat', $result['imported']],
                ['Dosen Diperbarui', $result['updated']],
                ['Baris Dilewati', $result['skipped']],
            ]
        );

        $this->info('Impor selesai!');

        return self::SUCCESS;
    }
}
