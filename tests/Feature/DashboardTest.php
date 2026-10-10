<?php

use App\Models\Book;
use App\Models\CommunityService;
use App\Models\Faculty;
use App\Models\IntellectualProperty;
use App\Models\Publication;
use App\Models\Research;
use App\Models\StudyProgram;

it('shows the admin dashboard with chart, faculty recap and queue', function () {
    $faculty = Faculty::factory()->create(['name' => 'Fakultas Kesehatan']);
    $lecturer = dosen(['study_program_id' => StudyProgram::factory()->create(['faculty_id' => $faculty->id])->id]);

    Research::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'year' => now()->year]);
    CommunityService::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'year' => now()->year - 1]);
    Publication::factory()->submitted()->withContributors()->create(['created_by' => $lecturer->id]);
    Book::factory()->draft()->withContributors()->create(['created_by' => $lecturer->id]);
    IntellectualProperty::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'year' => now()->year - 10]);

    $response = $this->actingAs(admin())->get(route('dashboard'))->assertOk()->assertSee('Fakultas Kesehatan');

    $modules = $response->viewData('modules');
    expect($modules['research']['verified_this_year'])->toBe(1)
        ->and($modules['community_service']['verified_total'])->toBe(1)
        ->and($modules['publication']['verified_total'])->toBe(0)
        ->and($modules['intellectual_property']['verified_total'])->toBe(1)
        // Ten years ago is outside the five-year chart.
        ->and(array_sum($modules['intellectual_property']['chart']))->toBe(0)
        ->and($response->viewData('pendingCount'))->toBe(1);

    // Workload chart counts every status: series order = Draf, Diajukan, Perlu Revisi, Terverifikasi.
    $statusSeries = collect($response->viewData('statusChart')['series'])->pluck('data', 'label');
    expect($statusSeries['Diajukan'][2])->toBe(1)
        ->and($statusSeries['Draf'][3])->toBe(1)
        ->and($response->viewData('composition')['data'])->toBe([1, 1, 0, 0, 1]);

    $recap = $response->viewData('facultyRecap')->firstWhere('name', 'Fakultas Kesehatan');
    expect($recap['total'])->toBe(3);
});

it('shows a lecturer only their own numbers, revisions, drafts and latest records', function () {
    $me = dosen();
    Research::factory()->verified()->withContributors()->create(['created_by' => $me->id, 'year' => now()->year]);
    Research::factory()->needsRevision()->withContributors()->create(['created_by' => $me->id, 'title' => 'Perlu Revisi Saya']);
    Book::factory()->draft()->withContributors()->create(['created_by' => $me->id, 'title' => 'Draf Buku Saya']);
    Research::factory()->verified()->withContributors()->count(3)->create(['year' => now()->year]);

    $response = $this->actingAs($me)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Perlu Revisi Saya')
        ->assertSee('Draf Buku Saya')
        ->assertSee('Data terbaru saya')
        ->assertDontSee('Rekap per fakultas');

    expect($response->viewData('modules')['research']['verified_this_year'])->toBe(1)
        ->and($response->viewData('latest'))->toHaveCount(3);
});
