<?php

use App\Enums\VerificationStatus;
use App\Models\Research;
use App\Models\Scheme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

it('redirects guests to login', function () {
    $this->get(route('researches.index'))->assertRedirect(route('login'));
});

it('lets an admin create, view, update and delete research — verified directly', function () {
    $admin = admin();
    $lead = dosen();

    $this->actingAs($admin)->get(route('researches.create'))->assertOk();

    $this->post(route('researches.store'), researchPayload($lead))->assertSessionHasNoErrors();
    $research = Research::firstOrFail();

    expect($research->status)->toBe(VerificationStatus::Verified)
        ->and($research->verified_by)->toBe($admin->id)
        ->and($research->contributors()->first()->user_id)->toBe($lead->id)
        ->and($research->verificationLogs()->count())->toBe(1);

    $this->get(route('researches.show', $research))->assertOk()->assertSee($research->title);
    $this->get(route('researches.edit', $research))->assertOk();

    $this->put(route('researches.update', $research), researchPayload($lead, ['title' => 'Judul Baru']))->assertSessionHasNoErrors();
    expect($research->fresh()->title)->toBe('Judul Baru');

    $this->delete(route('researches.destroy', $research))->assertRedirect(route('researches.index'));
    expect(Research::count())->toBe(0)->and(Research::withTrashed()->count())->toBe(1);
});

it('validates required fields, year range and scheme type', function (array $override, string $field) {
    $this->actingAs(admin())->post(route('researches.store'), researchPayload(dosen(), $override))->assertSessionHasErrors($field);
})->with([
    'judul' => [['title' => ''], 'title'],
    'tahun terlalu lama' => [['year' => 1980], 'year'],
    'tahun terlalu jauh' => [['year' => now()->year + 2], 'year'],
    'skema pengabdian' => [fn () => ['scheme_id' => Scheme::factory()->communityService()->create()->id], 'scheme_id'],
    'skema nonaktif' => [fn () => ['scheme_id' => Scheme::factory()->inactive()->create()->id], 'scheme_id'],
    'tanpa anggota' => [['contributors' => []], 'contributors'],
]);

it('saves a lecturer\'s new research as a draft with themself as Ketua', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('researches.store'), researchPayload(null, ['contributors' => [['name' => 'Mahasiswa A']]]))
        ->assertSessionHasNoErrors();

    $research = Research::firstOrFail();
    expect($research->status)->toBe(VerificationStatus::Draft)
        ->and($research->created_by)->toBe($lecturer->id)
        ->and($research->contributors()->pluck('user_id')->filter()->all())->toContain($lecturer->id);
});

it('ignores a status smuggled into the request', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)->post(route('researches.store'), researchPayload($lecturer, ['status' => 'VERIFIED']));

    expect(Research::firstOrFail()->status)->toBe(VerificationStatus::Draft);
});

it('lets the owner edit their draft but not a submitted or verified record', function (string $state, int $expected) {
    $lecturer = dosen();
    $research = Research::factory()->{$state}()->withContributors()->create(['created_by' => $lecturer->id]);

    $this->actingAs($lecturer)->get(route('researches.edit', $research))->assertStatus($expected);
    $this->put(route('researches.update', $research), researchPayload($lecturer))->assertStatus($expected === 200 ? 302 : 403);
    $this->delete(route('researches.destroy', $research))->assertStatus($expected === 200 ? 302 : 403);
})->with([
    'draf' => ['draft', 200],
    'perlu revisi' => ['needsRevision', 200],
    'diajukan' => ['submitted', 403],
    'terverifikasi' => ['verified', 403],
]);

it('lets the lead contributor edit a record someone else created', function () {
    $lead = dosen();
    $research = Research::factory()->create();
    $research->contributors()->create(['user_id' => $lead->id, 'sort_order' => 1]);

    $this->actingAs($lead)->get(route('researches.edit', $research))->assertOk();
});

it('lets a member view but not edit', function () {
    $member = dosen();
    $research = Research::factory()->withContributors([$member])->create();

    $this->actingAs($member)->get(route('researches.show', $research))->assertOk();
    $this->get(route('researches.edit', $research))->assertForbidden();
});

it('hides other lecturers\' records', function () {
    $me = dosen();
    $mine = Research::factory()->withContributors()->create(['created_by' => $me->id, 'title' => 'Penelitian Milik Saya']);
    $other = Research::factory()->withContributors()->create(['title' => 'Penelitian Dosen Lain']);

    $this->actingAs($me)->get(route('researches.index'))
        ->assertOk()
        ->assertSee('Penelitian Milik Saya')
        ->assertDontSee('Penelitian Dosen Lain');

    $this->get(route('researches.show', $other))->assertForbidden();
    $this->get(route('researches.edit', $other))->assertForbidden();
    $this->put(route('researches.update', $other), researchPayload($me))->assertForbidden();
    $this->delete(route('researches.destroy', $other))->assertForbidden();
    expect($mine)->not->toBeNull();
});

it('filters the list by year, status and keyword', function () {
    $admin = admin();
    Research::factory()->withContributors()->verified()->create(['title' => 'Alpha', 'year' => 2023]);
    Research::factory()->withContributors()->draft()->create(['title' => 'Beta', 'year' => 2024]);

    $this->actingAs($admin)->get(route('researches.index', ['year' => 2023]))->assertSee('Alpha')->assertDontSee('Beta');
    $this->get(route('researches.index', ['status' => 'DRAFT']))->assertSee('Beta')->assertDontSee('Alpha');
    $this->get(route('researches.index', ['q' => 'alp']))->assertSee('Alpha')->assertDontSee('Beta');
});

it('stores uploaded PDFs on the private disk and rejects other files', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('researches.store'), researchPayload($lecturer, [
            'documents' => [UploadedFile::fake()->create('laporan.pdf', 200, 'application/pdf')],
        ]))
        ->assertSessionHasNoErrors();

    $document = Research::firstOrFail()->documents()->firstOrFail();
    expect($document->original_name)->toBe('laporan.pdf');
    Storage::disk('local')->assertExists($document->path);

    $this->post(route('researches.store'), researchPayload($lecturer, [
        'documents' => [UploadedFile::fake()->create('foto.jpg', 200, 'image/jpeg')],
    ]))->assertSessionHasErrors('documents.0');
});

it('limits the number of documents', function () {
    config(['bp2m.documents.max_files' => 2]);
    $lecturer = dosen();

    $this->actingAs($lecturer)->post(route('researches.store'), researchPayload($lecturer, [
        'documents' => array_map(fn ($i) => UploadedFile::fake()->create("d{$i}.pdf", 10, 'application/pdf'), [1, 2, 3]),
    ]))->assertSessionHasErrors('documents');
});
