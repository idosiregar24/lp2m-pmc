<?php

use App\Enums\VerificationStatus;
use App\Models\CommunityService;
use App\Models\Scheme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

it('redirects guests to login', function () {
    $this->get(route('community-services.index'))->assertRedirect(route('login'));
});

it('lets an admin create, view, update and delete — verified directly', function () {
    $admin = admin();
    $lead = dosen();

    $this->actingAs($admin)->get(route('community-services.index'))->assertOk();
    $this->get(route('community-services.create'))->assertOk();

    $this->post(route('community-services.store'), communityServicePayload($lead))->assertSessionHasNoErrors();
    $record = CommunityService::firstOrFail();

    expect($record->status)->toBe(VerificationStatus::Verified)
        ->and($record->verified_by)->toBe($admin->id)
        ->and($record->contributors()->first()->user_id)->toBe($lead->id);

    $this->get(route('community-services.show', $record))->assertOk()->assertSee($record->title);
    $this->get(route('community-services.edit', $record))->assertOk();
    $this->get(route('community-services.index'))->assertSee($record->title);

    $this->put(route('community-services.update', $record), communityServicePayload($lead, ['title' => 'Judul Baru']))->assertSessionHasNoErrors();
    expect($record->fresh()->title)->toBe('Judul Baru');

    $this->delete(route('community-services.destroy', $record))->assertRedirect(route('community-services.index'));
    expect(CommunityService::count())->toBe(0)->and(CommunityService::withTrashed()->count())->toBe(1);
});

it('validates the fields', function (array $override, string $field) {
    $this->actingAs(admin())->post(route('community-services.store'), communityServicePayload(dosen(), $override))->assertSessionHasErrors($field);
})->with([
    'judul' => [['title' => ''], 'title'],
    'tahun terlalu lama' => [['year' => 1980], 'year'],
    'tahun terlalu jauh' => [['year' => now()->year + 2], 'year'],
    'tanpa anggota' => [['contributors' => []], 'contributors'],
    'mitra' => [['partner' => ''], 'partner'],
    'skema penelitian' => [fn () => ['scheme_id' => Scheme::factory()->create()->id], 'scheme_id'],
    'skema nonaktif' => [fn () => ['scheme_id' => Scheme::factory()->communityService()->inactive()->create()->id], 'scheme_id'],
]);

it('saves a lecturer\'s new record as a draft with themself as first member', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('community-services.store'), communityServicePayload(null, ['contributors' => [['name' => 'Pihak Luar']], 'status' => 'VERIFIED']))
        ->assertSessionHasNoErrors();

    $record = CommunityService::firstOrFail();
    expect($record->status)->toBe(VerificationStatus::Draft)
        ->and($record->created_by)->toBe($lecturer->id)
        ->and($record->contributors()->pluck('user_id')->filter()->all())->toContain($lecturer->id);
});

it('lets the owner edit a draft but not a submitted or verified record', function (string $state, bool $editable) {
    $lecturer = dosen();
    $record = CommunityService::factory()->{$state}()->withContributors()->create(['created_by' => $lecturer->id]);

    $this->actingAs($lecturer)->get(route('community-services.edit', $record))->assertStatus($editable ? 200 : 403);
    $this->put(route('community-services.update', $record), communityServicePayload($lecturer))->assertStatus($editable ? 302 : 403);
})->with([
    'draf' => ['draft', true],
    'perlu revisi' => ['needsRevision', true],
    'diajukan' => ['submitted', false],
    'terverifikasi' => ['verified', false],
]);

it('hides other lecturers\' records', function () {
    $me = dosen();
    CommunityService::factory()->withContributors()->create(['created_by' => $me->id, 'title' => 'Data Milik Saya']);
    $other = CommunityService::factory()->withContributors()->create(['title' => 'Data Dosen Lain']);

    $this->actingAs($me)->get(route('community-services.index'))
        ->assertOk()
        ->assertSee('Data Milik Saya')
        ->assertDontSee('Data Dosen Lain');

    $this->get(route('community-services.show', $other))->assertForbidden();
    $this->get(route('community-services.edit', $other))->assertForbidden();
    $this->put(route('community-services.update', $other), communityServicePayload($me))->assertForbidden();
    $this->delete(route('community-services.destroy', $other))->assertForbidden();
});

it('stores a PDF, submits for verification and gets approved', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('community-services.store'), communityServicePayload($lecturer, [
            'submit' => '1',
            'documents' => [UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')],
        ]))
        ->assertSessionHasNoErrors();

    $record = CommunityService::firstOrFail();
    expect($record->status)->toBe(VerificationStatus::Submitted);
    Storage::disk('local')->assertExists($record->documents()->first()->path);

    $this->actingAs(admin())->get(route('verifications.index'))->assertSee($record->title);
    $this->post(route('verifications.approve', ['type' => 'community_service', 'id' => $record->id]));

    expect($record->fresh()->status)->toBe(VerificationStatus::Verified)
        ->and($record->verificationLogs()->count())->toBe(2);
});

it('rejects a non-PDF document', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)->post(route('community-services.store'), communityServicePayload($lecturer, [
        'documents' => [UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg')],
    ]))->assertSessionHasErrors('documents.0');
});

it('filters by status and searches the module text columns', function () {
    $this->actingAs(admin());
    CommunityService::factory()->withContributors()->verified()->create(['title' => 'Alpha', ...['partner' => 'Posyandu Melati']]);
    CommunityService::factory()->withContributors()->draft()->create(['title' => 'Beta']);

    $this->get(route('community-services.index', ['q' => 'posyandu']))->assertSee('Alpha')->assertDontSee('Beta');
    $this->get(route('community-services.index', ['status' => 'DRAFT']))->assertSee('Beta')->assertDontSee('Alpha');
});
