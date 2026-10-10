<?php

use App\Enums\VerificationStatus;
use App\Models\IntellectualProperty;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

it('redirects guests to login', function () {
    $this->get(route('intellectual-properties.index'))->assertRedirect(route('login'));
});

it('lets an admin create, view, update and delete — verified directly', function () {
    $admin = admin();
    $lead = dosen();

    $this->actingAs($admin)->get(route('intellectual-properties.index'))->assertOk();
    $this->get(route('intellectual-properties.create'))->assertOk();

    $this->post(route('intellectual-properties.store'), intellectualPropertyPayload($lead))->assertSessionHasNoErrors();
    $record = IntellectualProperty::firstOrFail();

    expect($record->status)->toBe(VerificationStatus::Verified)
        ->and($record->verified_by)->toBe($admin->id)
        ->and($record->contributors()->first()->user_id)->toBe($lead->id);

    $this->get(route('intellectual-properties.show', $record))->assertOk()->assertSee($record->title);
    $this->get(route('intellectual-properties.edit', $record))->assertOk();
    $this->get(route('intellectual-properties.index'))->assertSee($record->title);

    $this->put(route('intellectual-properties.update', $record), intellectualPropertyPayload($lead, ['title' => 'Judul Baru']))->assertSessionHasNoErrors();
    expect($record->fresh()->title)->toBe('Judul Baru');

    $this->delete(route('intellectual-properties.destroy', $record))->assertRedirect(route('intellectual-properties.index'));
    expect(IntellectualProperty::count())->toBe(0)->and(IntellectualProperty::withTrashed()->count())->toBe(1);
});

it('validates the fields', function (array $override, string $field) {
    $this->actingAs(admin())->post(route('intellectual-properties.store'), intellectualPropertyPayload(dosen(), $override))->assertSessionHasErrors($field);
})->with([
    'judul' => [['title' => ''], 'title'],
    'tahun terlalu lama' => [['year' => 1980], 'year'],
    'tahun terlalu jauh' => [['year' => now()->year + 2], 'year'],
    'tanpa anggota' => [['contributors' => []], 'contributors'],
    'jenis KI' => [['type' => ''], 'type'],
    'jenis KI asing' => [['type' => 'DOMAIN_NAME'], 'type'],
]);

it('saves a lecturer\'s new record as a draft with themself as first member', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('intellectual-properties.store'), intellectualPropertyPayload(null, ['contributors' => [['name' => 'Pihak Luar']], 'status' => 'VERIFIED']))
        ->assertSessionHasNoErrors();

    $record = IntellectualProperty::firstOrFail();
    expect($record->status)->toBe(VerificationStatus::Draft)
        ->and($record->created_by)->toBe($lecturer->id)
        ->and($record->contributors()->pluck('user_id')->filter()->all())->toContain($lecturer->id);
});

it('lets the owner edit a draft but not a submitted or verified record', function (string $state, bool $editable) {
    $lecturer = dosen();
    $record = IntellectualProperty::factory()->{$state}()->withContributors()->create(['created_by' => $lecturer->id]);

    $this->actingAs($lecturer)->get(route('intellectual-properties.edit', $record))->assertStatus($editable ? 200 : 403);
    $this->put(route('intellectual-properties.update', $record), intellectualPropertyPayload($lecturer))->assertStatus($editable ? 302 : 403);
})->with([
    'draf' => ['draft', true],
    'perlu revisi' => ['needsRevision', true],
    'diajukan' => ['submitted', false],
    'terverifikasi' => ['verified', false],
]);

it('hides other lecturers\' records', function () {
    $me = dosen();
    IntellectualProperty::factory()->withContributors()->create(['created_by' => $me->id, 'title' => 'Data Milik Saya']);
    $other = IntellectualProperty::factory()->withContributors()->create(['title' => 'Data Dosen Lain']);

    $this->actingAs($me)->get(route('intellectual-properties.index'))
        ->assertOk()
        ->assertSee('Data Milik Saya')
        ->assertDontSee('Data Dosen Lain');

    $this->get(route('intellectual-properties.show', $other))->assertForbidden();
    $this->get(route('intellectual-properties.edit', $other))->assertForbidden();
    $this->put(route('intellectual-properties.update', $other), intellectualPropertyPayload($me))->assertForbidden();
    $this->delete(route('intellectual-properties.destroy', $other))->assertForbidden();
});

it('stores a PDF, submits for verification and gets approved', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('intellectual-properties.store'), intellectualPropertyPayload($lecturer, [
            'submit' => '1',
            'documents' => [UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')],
        ]))
        ->assertSessionHasNoErrors();

    $record = IntellectualProperty::firstOrFail();
    expect($record->status)->toBe(VerificationStatus::Submitted);
    Storage::disk('local')->assertExists($record->documents()->first()->path);

    $this->actingAs(admin())->get(route('verifications.index'))->assertSee($record->title);
    $this->post(route('verifications.approve', ['type' => 'intellectual_property', 'id' => $record->id]));

    expect($record->fresh()->status)->toBe(VerificationStatus::Verified)
        ->and($record->verificationLogs()->count())->toBe(2);
});

it('rejects a non-PDF document', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)->post(route('intellectual-properties.store'), intellectualPropertyPayload($lecturer, [
        'documents' => [UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg')],
    ]))->assertSessionHasErrors('documents.0');
});
