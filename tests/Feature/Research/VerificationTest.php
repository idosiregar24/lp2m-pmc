<?php

use App\Enums\VerificationStatus;
use App\Exceptions\InvalidStatusTransition;
use App\Models\Research;
use App\Models\VerificationLog;
use App\Services\ResearchService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

it('submits with "Simpan & Ajukan" when a document is attached', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)->post(route('researches.store'), researchPayload($lecturer, [
        'submit' => '1',
        'documents' => [UploadedFile::fake()->create('kontrak.pdf', 50, 'application/pdf')],
    ]))->assertSessionHasNoErrors();

    $research = Research::firstOrFail();
    expect($research->status)->toBe(VerificationStatus::Submitted)
        ->and($research->submitted_at)->not->toBeNull()
        ->and($research->verificationLogs()->first()->to_status)->toBe(VerificationStatus::Submitted);
});

it('refuses to submit without any document', function () {
    $lecturer = dosen();

    $this->actingAs($lecturer)
        ->post(route('researches.store'), researchPayload($lecturer, ['submit' => '1']))
        ->assertSessionHasErrors('documents');

    $draft = Research::factory()->withContributors()->create(['created_by' => $lecturer->id]);
    $this->post(route('submissions.store', ['research', $draft->id]))->assertSessionHas('error');
    expect($draft->fresh()->status)->toBe(VerificationStatus::Draft);
});

it('submits a draft from the detail page', function () {
    $lecturer = dosen();
    $research = Research::factory()->withContributors()->withDocument()->create(['created_by' => $lecturer->id]);

    $this->actingAs($lecturer)->get(route('researches.show', $research))->assertSee('Ajukan Verifikasi');
    $this->post(route('submissions.store', ['research', $research->id]))->assertSessionHas('success');

    expect($research->fresh()->status)->toBe(VerificationStatus::Submitted);
});

it('lets an admin approve a submitted record', function () {
    $admin = admin();
    $research = Research::factory()->withContributors()->submitted()->create();

    $this->actingAs($admin)->get(route('verifications.index'))->assertOk()->assertSee($research->title);
    $this->get(route('researches.show', $research))->assertSee('Kembalikan untuk Revisi');

    $this->post(route('verifications.approve', ['research', $research->id]))->assertSessionHas('success');

    $research->refresh();
    expect($research->status)->toBe(VerificationStatus::Verified)
        ->and($research->verified_by)->toBe($admin->id);
});

it('sends a record back with a required note, then the owner resubmits', function () {
    $lecturer = dosen();
    $research = Research::factory()->withContributors()->withDocument()->submitted()->create(['created_by' => $lecturer->id]);

    $this->actingAs(admin())->post(route('verifications.return', ['research', $research->id]), ['note' => ''])->assertSessionHasErrors('note');
    $this->post(route('verifications.return', ['research', $research->id]), ['note' => 'Kontrak belum ditandatangani.'])->assertSessionHas('success');

    expect($research->fresh())
        ->status->toBe(VerificationStatus::NeedsRevision)
        ->revision_note->toBe('Kontrak belum ditandatangani.');

    $this->actingAs($lecturer)->get(route('dashboard'))->assertSee('Kontrak belum ditandatangani.');
    $this->put(route('researches.update', $research), researchPayload($lecturer, ['submit' => '1']))->assertSessionHasNoErrors();

    expect($research->fresh()->status)->toBe(VerificationStatus::Submitted);
});

it('reopens a verified record', function () {
    $research = Research::factory()->withContributors()->verified()->create();

    $this->actingAs(admin())->post(route('verifications.reopen', ['research', $research->id]), ['note' => 'Tahun salah.'])->assertSessionHas('success');

    expect($research->fresh()->status)->toBe(VerificationStatus::NeedsRevision);
});

it('refuses illegal transitions', function () {
    $draft = Research::factory()->withContributors()->create();

    $this->actingAs(admin())->post(route('verifications.approve', ['research', $draft->id]))->assertForbidden();
    expect($draft->fresh()->status)->toBe(VerificationStatus::Draft);
});

it('keeps verification actions admin-only', function () {
    $lecturer = dosen();
    $research = Research::factory()->withContributors()->submitted()->create(['created_by' => $lecturer->id]);

    $this->actingAs($lecturer)->get(route('verifications.index'))->assertForbidden();
    $this->post(route('verifications.approve', ['research', $research->id]))->assertForbidden();
    $this->post(route('submissions.store', ['research', $research->id]))->assertForbidden();
});

it('returns 404 for an unknown module alias', function () {
    $this->actingAs(admin())->post(route('verifications.approve', ['App\\Models\\User', 1]))->assertNotFound();
});

it('writes an append-only log for every transition', function () {
    $research = Research::factory()->withContributors()->submitted()->create();

    $this->actingAs(admin())->post(route('verifications.approve', ['research', $research->id]));

    $log = VerificationLog::firstOrFail();
    expect($log->from_status)->toBe(VerificationStatus::Submitted)->and($log->to_status)->toBe(VerificationStatus::Verified);
    expect(fn () => $log->update(['note' => 'x']))->toThrow(LogicException::class);
    expect(fn () => $log->delete())->toThrow(LogicException::class);
});

it('counts only verified records on the dashboard', function () {
    $lecturer = dosen();
    Research::factory()->withContributors()->verified()->create(['created_by' => $lecturer->id, 'year' => now()->year]);
    Research::factory()->withContributors()->draft()->create(['created_by' => $lecturer->id, 'year' => now()->year]);

    $this->actingAs($lecturer)->get(route('dashboard'))->assertOk()->assertSee('Total: 1');
});

it('refuses an owner update once the record was submitted in the meantime', function () {
    $owner = dosen();
    $research = Research::factory()->withContributors()->create(['created_by' => $owner->id]);
    $research->forceFill(['status' => VerificationStatus::Submitted])->save();

    expect(fn () => app(ResearchService::class)->update($research, researchPayload($owner), [], $owner))
        ->toThrow(InvalidStatusTransition::class);

    expect($research->fresh()->title)->not->toBe(researchPayload()['title']);
});

it('keeps the typed input when a submission is refused', function () {
    $owner = dosen();
    $research = Research::factory()->withContributors()->create(['created_by' => $owner->id]);

    $this->actingAs($owner)->from(route('researches.show', $research))
        ->post(route('submissions.store', ['type' => 'research', 'id' => $research->id]), ['note' => 'x'])
        ->assertSessionHas('error')
        ->assertSessionHasInput('note', 'x');
});
