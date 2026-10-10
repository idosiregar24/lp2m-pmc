<?php

use App\Enums\VerificationStatus;
use App\Models\Publication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

it('redirects guests to login', function () {
    $this->get(route('publications.index'))->assertRedirect(route('login'));
});

it('lets an admin create, view, update and delete — verified directly', function () {
    $admin = admin();
    $lead = dosen();

    $this->actingAs($admin)->get(route('publications.index'))->assertOk();
    $this->get(route('publications.create'))->assertOk();

    $this->post(route('publications.store'), publicationPayload($lead))->assertSessionHasNoErrors();
    $record = Publication::firstOrFail();

    expect($record->status)->toBe(VerificationStatus::Verified)
        ->and($record->verified_by)->toBe($admin->id)
        ->and($record->contributors()->first()->user_id)->toBe($lead->id);

    $this->get(route('publications.show', $record))->assertOk()->assertSee($record->title);
    $this->get(route('publications.edit', $record))->assertOk();
    $this->get(route('publications.index'))->assertSee($record->title);

    $this->put(route('publications.update', $record), publicationPayload($lead, ['title' => 'Judul Baru']))->assertSessionHasNoErrors();
    expect($record->fresh()->title)->toBe('Judul Baru');

    $this->delete(route('publications.destroy', $record))->assertRedirect(route('publications.index'));
    expect(Publication::count())->toBe(0)->and(Publication::withTrashed()->count())->toBe(1);
});

it('validates the fields', function (array $override, string $field) {
    $this->actingAs(admin())->post(route('publications.store'), publicationPayload(dosen(), $override))->assertSessionHasErrors($field);
})->with([
    'judul' => [['title' => ''], 'title'],
    'tahun terlalu lama' => [['year' => 1980], 'year'],
    'tahun terlalu jauh' => [['year' => now()->year + 2], 'year'],
    'tanpa anggota' => [['contributors' => []], 'contributors'],
    'nama jurnal' => [['venue_name' => ''], 'venue_name'],
    'penerbit' => [['publisher' => ''], 'publisher'],
    'jenis wadah asing' => [['venue_type' => 'MAGAZINE'], 'venue_type'],
    'level asing' => [['level' => 'LOCAL'], 'level'],
]);

it('saves a lecturer\'s new record as a draft with themself as first member', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('publications.store'), publicationPayload(null, ['contributors' => [['name' => 'Pihak Luar']], 'status' => 'VERIFIED']))
        ->assertSessionHasNoErrors();

    $record = Publication::firstOrFail();
    expect($record->status)->toBe(VerificationStatus::Draft)
        ->and($record->created_by)->toBe($lecturer->id)
        ->and($record->contributors()->pluck('user_id')->filter()->all())->toContain($lecturer->id);
});

it('lets the owner edit a draft but not a submitted or verified record', function (string $state, bool $editable) {
    $lecturer = dosen();
    $record = Publication::factory()->{$state}()->withContributors()->create(['created_by' => $lecturer->id]);

    $this->actingAs($lecturer)->get(route('publications.edit', $record))->assertStatus($editable ? 200 : 403);
    $this->put(route('publications.update', $record), publicationPayload($lecturer))->assertStatus($editable ? 302 : 403);
})->with([
    'draf' => ['draft', true],
    'perlu revisi' => ['needsRevision', true],
    'diajukan' => ['submitted', false],
    'terverifikasi' => ['verified', false],
]);

it('hides other lecturers\' records', function () {
    $me = dosen();
    Publication::factory()->withContributors()->create(['created_by' => $me->id, 'title' => 'Data Milik Saya']);
    $other = Publication::factory()->withContributors()->create(['title' => 'Data Dosen Lain']);

    $this->actingAs($me)->get(route('publications.index'))
        ->assertOk()
        ->assertSee('Data Milik Saya')
        ->assertDontSee('Data Dosen Lain');

    $this->get(route('publications.show', $other))->assertForbidden();
    $this->get(route('publications.edit', $other))->assertForbidden();
    $this->put(route('publications.update', $other), publicationPayload($me))->assertForbidden();
    $this->delete(route('publications.destroy', $other))->assertForbidden();
});

it('stores a PDF, submits for verification and gets approved', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('publications.store'), publicationPayload($lecturer, [
            'submit' => '1',
            'documents' => [UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')],
        ]))
        ->assertSessionHasNoErrors();

    $record = Publication::firstOrFail();
    expect($record->status)->toBe(VerificationStatus::Submitted);
    Storage::disk('local')->assertExists($record->documents()->first()->path);

    $this->actingAs(admin())->get(route('verifications.index'))->assertSee($record->title);
    $this->post(route('verifications.approve', ['type' => 'publication', 'id' => $record->id]));

    expect($record->fresh()->status)->toBe(VerificationStatus::Verified)
        ->and($record->verificationLogs()->count())->toBe(2);
});

it('rejects a non-PDF document', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)->post(route('publications.store'), publicationPayload($lecturer, [
        'documents' => [UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg')],
    ]))->assertSessionHasErrors('documents.0');
});

it('filters by status and searches the module text columns', function () {
    $this->actingAs(admin());
    Publication::factory()->withContributors()->verified()->create(['title' => 'Alpha', ...['venue_name' => 'Jurnal Keperawatan Melati']]);
    Publication::factory()->withContributors()->draft()->create(['title' => 'Beta']);

    $this->get(route('publications.index', ['q' => 'keperawatan melati']))->assertSee('Alpha')->assertDontSee('Beta');
    $this->get(route('publications.index', ['status' => 'DRAFT']))->assertSee('Beta')->assertDontSee('Alpha');
});
