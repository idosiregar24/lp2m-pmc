<?php

use App\Enums\SchemeType;
use App\Models\CommunityService;
use App\Models\Faculty;
use App\Models\FundingSource;
use App\Models\Research;
use App\Models\Scheme;
use App\Models\StudyProgram;

it('renders every master page for admins and forbids lecturers', function (string $route) {
    $this->actingAs(admin())->get(route($route))->assertOk();
    $this->actingAs(dosen())->get(route($route))->assertForbidden();
})->with([
    'master.faculties.index', 'master.faculties.create',
    'master.study-programs.index', 'master.study-programs.create',
    'master.schemes.index', 'master.schemes.create',
    'master.funding-sources.index', 'master.funding-sources.create',
]);

it('creates and updates a faculty', function () {
    $this->actingAs(admin())
        ->post(route('master.faculties.store'), ['name' => 'Fakultas Kedokteran', 'code' => 'FK', 'is_active' => '1'])
        ->assertRedirect(route('master.faculties.index'));

    $faculty = Faculty::where('name', 'Fakultas Kedokteran')->firstOrFail();

    $this->put(route('master.faculties.update', $faculty), ['name' => 'Fakultas Kedokteran', 'code' => 'FK', 'is_active' => '0'])
        ->assertSessionHasNoErrors();

    expect($faculty->fresh()->is_active)->toBeFalse();
});

it('creates a study program and a scheme', function () {
    $faculty = Faculty::factory()->create();

    $this->actingAs(admin())
        ->post(route('master.study-programs.store'), ['faculty_id' => $faculty->id, 'name' => 'Keperawatan', 'level' => 'S1', 'is_active' => '1'])
        ->assertSessionHasNoErrors();
    $this->post(route('master.schemes.store'), ['type' => 'RESEARCH', 'name' => 'Penelitian Dasar', 'is_active' => '1'])
        ->assertSessionHasNoErrors();
    $this->post(route('master.funding-sources.store'), ['name' => 'Mandiri', 'is_active' => '1'])
        ->assertSessionHasNoErrors();

    expect(StudyProgram::where('name', 'Keperawatan')->exists())->toBeTrue()
        ->and(Scheme::where('name', 'Penelitian Dasar')->first()->type)->toBe(SchemeType::Research)
        ->and(FundingSource::where('name', 'Mandiri')->exists())->toBeTrue();
});

it('refuses to delete masters that are in use', function () {
    $research = Research::factory()->create();

    $this->actingAs(admin())->delete(route('master.schemes.destroy', $research->scheme_id))->assertSessionHas('error');
    $this->delete(route('master.funding-sources.destroy', $research->funding_source_id))->assertSessionHas('error');

    $program = $research->creator->studyProgram;
    $this->delete(route('master.study-programs.destroy', $program))->assertSessionHas('error');
    $this->delete(route('master.faculties.destroy', $program->faculty_id))->assertSessionHas('error');

    expect(Scheme::find($research->scheme_id))->not->toBeNull();
});

it('treats masters used only by community services as in use', function () {
    $communityService = CommunityService::factory()->create();

    $this->actingAs(admin())->delete(route('master.schemes.destroy', $communityService->scheme_id))->assertSessionHas('error');
    $this->delete(route('master.funding-sources.destroy', $communityService->funding_source_id))->assertSessionHas('error');
});

it('deletes unused masters', function () {
    $faculty = Faculty::factory()->create();

    $this->actingAs(admin())->delete(route('master.faculties.destroy', $faculty))->assertRedirect(route('master.faculties.index'));

    expect(Faculty::find($faculty->id))->toBeNull();
});

it('hides inactive masters from form options but keeps the current value', function () {
    $active = Scheme::factory()->create();
    $inactive = Scheme::factory()->inactive()->create();

    expect(Scheme::options(SchemeType::Research))->toHaveKey($active->id)->not->toHaveKey($inactive->id)
        ->and(Scheme::options(SchemeType::Research, $inactive->id))->toHaveKey($inactive->id);
});
