<?php

use App\Models\Book;
use App\Models\Publication;
use App\Models\Research;
use App\Models\StudyProgram;

it('lets guests search lecturers by name and unit', function () {
    $program = StudyProgram::factory()->create();
    $match = dosen(['name' => 'Dr. Ani Publik', 'study_program_id' => $program->id]);
    dosen(['name' => 'Budi Lain']);
    admin(['name' => 'Admin Tersembunyi']);
    dosen(['name' => 'Dosen Nonaktif', 'is_active' => false]);
    Research::factory()->verified()->withContributors()->count(2)->create(['created_by' => $match->id]);

    $response = $this->get(route('public.lecturers.index'))
        ->assertOk()
        ->assertSee('Dr. Ani Publik')
        ->assertSee('Budi Lain')
        ->assertDontSee('Admin Tersembunyi')
        ->assertDontSee('Dosen Nonaktif');

    expect($response->viewData('rows')[$match->id]['counts']['research'])->toBe(2);

    $this->get(route('public.lecturers.index', ['q' => 'ani']))->assertSee('Dr. Ani Publik')->assertDontSee('Budi Lain');
    $this->get(route('public.lecturers.index', ['study_program' => $program->id]))->assertSee('Dr. Ani Publik')->assertDontSee('Budi Lain');
});

it('shows only verified records of a lecturer, without private data', function () {
    $lecturer = dosen(['name' => 'Dr. Ani Publik', 'nidn_nuptk' => '1234567890']);
    $research = Research::factory()->verified()->withContributors()->withDocument()->create(['created_by' => $lecturer->id, 'title' => 'Penelitian Terverifikasi']);
    $research->contributors()->create(['name' => 'Mitra Luar', 'affiliation' => 'Universitas X', 'sort_order' => 2]);
    Research::factory()->draft()->withContributors()->create(['created_by' => $lecturer->id, 'title' => 'Penelitian Draf']);
    Research::factory()->submitted()->withContributors()->create(['created_by' => $lecturer->id, 'title' => 'Penelitian Diajukan']);
    Research::factory()->verified()->withContributors()->create(['title' => 'Penelitian Orang Lain']);

    $this->get(route('public.lecturers.show', $lecturer))
        ->assertOk()
        ->assertSee('Penelitian Terverifikasi')
        ->assertSee('Mitra Luar')
        ->assertSee($research->scheme->name)
        ->assertDontSee('Penelitian Draf')
        ->assertDontSee('Penelitian Diajukan')
        ->assertDontSee('Penelitian Orang Lain')
        ->assertDontSee('1234567890')
        ->assertDontSee('/dokumen/', false);
});

it('switches module tabs and filters by year', function () {
    $lecturer = dosen();
    Publication::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'title' => 'Artikel 2023', 'year' => 2023]);
    Publication::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'title' => 'Artikel 2024', 'year' => 2024]);
    Book::factory()->verified()->withContributors([$lecturer])->create(['title' => 'Buku Bersama']);

    // Default tab = first module with data (Publikasi; no research).
    $this->get(route('public.lecturers.show', $lecturer))->assertSee('Artikel 2023')->assertDontSee('Buku Bersama');
    $this->get(route('public.lecturers.show', [$lecturer, 'modul' => 'book']))->assertSee('Buku Bersama')->assertDontSee('Artikel 2023');
    $this->get(route('public.lecturers.show', [$lecturer, 'modul' => 'publication', 'year' => 2024]))
        ->assertSee('Artikel 2024')->assertDontSee('Artikel 2023');
    $this->get(route('public.lecturers.show', [$lecturer, 'modul' => 'nope']))->assertOk();
});

it('hides admins and inactive lecturers', function () {
    $this->get(route('public.lecturers.show', admin()))->assertNotFound();
    $this->get(route('public.lecturers.show', dosen(['is_active' => false])))->assertNotFound();
    $this->get('/publik/dosen/999999')->assertNotFound();
});

it('renders every module tab', function (string $alias) {
    $this->get(route('public.lecturers.show', [dosen(), 'modul' => $alias]))->assertOk();
})->with(['research', 'community_service', 'publication', 'book', 'intellectual_property']);

it('links the public portal from the login page', function () {
    $this->get(route('login'))->assertSee(route('public.lecturers.index'));
});

it('filters lecturers by module output and sort order', function () {
    $lecturerWithResearch = dosen(['name' => 'Alice Riset']);
    $lecturerWithBookOnly = dosen(['name' => 'Zack Buku']);
    Research::factory()->verified()->withContributors()->create(['created_by' => $lecturerWithResearch->id]);
    Book::factory()->verified()->withContributors([$lecturerWithBookOnly])->create(['title' => 'Buku Zack']);

    $response = $this->get(route('public.lecturers.index', ['has_modul' => 'research']))
        ->assertOk()
        ->assertSee('Alice Riset')
        ->assertDontSee('Zack Buku');

    $sortResponse = $this->get(route('public.lecturers.index', ['sort' => 'name_desc']))->assertOk();
    expect($sortResponse->status())->toBe(200);
});

it('searches and filters records inside a lecturer detail page by keyword and role', function () {
    $lecturer = dosen(['name' => 'Dr. Peneliti Handal']);
    $leadResearch = Research::factory()->verified()->withContributors()->create([
        'created_by' => $lecturer->id,
        'title' => 'Studi Efektivitas Vaksin PMC',
        'year' => 2024,
    ]);
    $memberResearch = Research::factory()->verified()->create([
        'title' => 'Analisis Kebijakan Kesehatan',
        'year' => 2024,
    ]);
    $memberResearch->contributors()->createMany([
        ['name' => 'Prof. Utama', 'affiliation' => 'PMC', 'sort_order' => 1],
        ['user_id' => $lecturer->id, 'sort_order' => 2],
    ]);

    // Search by title keyword
    $this->get(route('public.lecturers.show', [$lecturer, 'modul' => 'research', 'q' => 'Vaksin']))
        ->assertSee('Studi Efektivitas Vaksin PMC')
        ->assertDontSee('Analisis Kebijakan Kesehatan');

    // Filter by role: lead
    $this->get(route('public.lecturers.show', [$lecturer, 'modul' => 'research', 'role' => 'lead']))
        ->assertSee('Studi Efektivitas Vaksin PMC')
        ->assertDontSee('Analisis Kebijakan Kesehatan');

    // Filter by role: member
    $this->get(route('public.lecturers.show', [$lecturer, 'modul' => 'research', 'role' => 'member']))
        ->assertSee('Analisis Kebijakan Kesehatan')
        ->assertDontSee('Studi Efektivitas Vaksin PMC');
});
