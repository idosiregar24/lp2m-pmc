<?php

return [

    /*
    | Rows per page on every list page.
    */
    'per_page' => 15,

    /*
    | Lowest year accepted for research data (App\Rules\ValidYear).
    */
    'min_year' => 1990,

    /*
    | Supporting documents (PRD §6.2) — private PDFs on the "local" disk.
    */
    'documents' => [
        'max_kb' => 10240,
        'max_files' => 10,
        'mimes' => ['pdf'],
    ],

    /*
    | Admin verification (PRD §4.11). [ASUMSI] submitting needs ≥1 document.
    */
    'verification' => [
        'require_document' => true,
    ],

    /*
    | Logo uploaded from Pengaturan (PRD §4.12) — the only file on the "public" disk.
    */
    'logo' => [
        'max_kb' => 1024,
        'mimes' => ['png', 'jpg', 'jpeg', 'webp'],
        'min_size' => 128,
    ],

    /*
    | Password of the first admin created by ProductionSeeder.
    */
    'admin_initial_password' => env('ADMIN_INITIAL_PASSWORD'),

];
