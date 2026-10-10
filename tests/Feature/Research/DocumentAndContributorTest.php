<?php

use App\Models\Research;
use App\Services\ContributorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

beforeEach(fn () => Storage::fake('local'));

function researchWithFile(array $attributes = []): Research
{
    $research = Research::factory()->withContributors()->create($attributes);
    $path = UploadedFile::fake()->create('laporan.pdf', 20, 'application/pdf')->storeAs("documents/research/{$research->id}", 'file.pdf', 'local');
    $research->documents()->create([
        'original_name' => 'laporan.pdf', 'path' => $path, 'mime_type' => 'application/pdf', 'size' => 20480, 'uploaded_by' => $research->created_by,
    ]);

    return $research;
}

it('downloads a document for those who may view the record', function () {
    $owner = dosen();
    $research = researchWithFile(['created_by' => $owner->id]);
    $document = $research->documents()->first();

    $this->actingAs($owner)->get(route('documents.download', $document))->assertOk()->assertDownload('laporan.pdf');
    $this->actingAs(admin())->get(route('documents.download', $document))->assertOk();
    $this->actingAs(dosen())->get(route('documents.download', $document))->assertForbidden();
});

it('deletes a document with its file only while the record is editable', function () {
    $owner = dosen();
    $research = researchWithFile(['created_by' => $owner->id]);
    $document = $research->documents()->first();

    $this->actingAs($owner)->delete(route('documents.destroy', $document))->assertSessionHas('success');

    expect($research->documents()->count())->toBe(0);
    Storage::disk('local')->assertMissing($document->path);

    $locked = researchWithFile(['created_by' => $owner->id, 'status' => 'VERIFIED']);
    $this->delete(route('documents.destroy', $locked->documents()->first()))->assertForbidden();
});

it('keeps contributor order and rejects duplicates', function () {
    $actor = admin();
    $a = dosen();
    $b = dosen();
    $research = Research::factory()->create();
    $service = app(ContributorService::class);

    $service->sync($research, [['user_id' => $b->id], ['name' => 'Luar', 'affiliation' => 'Univ X'], ['user_id' => $a->id]], $actor);

    expect($research->contributors()->get()->map(fn ($c) => [$c->sort_order, $c->user_id, $c->name])->all())
        ->toBe([[1, $b->id, null], [2, null, 'Luar'], [3, $a->id, null]]);

    expect(fn () => $service->sync($research, [['user_id' => $a->id], ['user_id' => $a->id]], $actor))
        ->toThrow(ValidationException::class);
});

it('does not let a lecturer remove themself from an existing record', function () {
    $lecturer = dosen();
    $research = Research::factory()->withContributors()->create(['created_by' => $lecturer->id]);

    $this->actingAs($lecturer)
        ->put(route('researches.update', $research), researchPayload(null))
        ->assertSessionHasErrors('contributors');
});

it('lets only an Admin download documents of a deleted record', function () {
    $owner = dosen();
    $research = researchWithFile(['created_by' => $owner->id]);
    $document = $research->documents()->first();
    $research->delete();

    $this->actingAs(admin())->get(route('documents.download', $document))->assertOk();
    $this->actingAs($owner)->get(route('documents.download', $document))->assertForbidden();
});

it('keeps a record saveable after a contributor became Admin', function () {
    $owner = dosen();
    $member = dosen();
    $research = Research::factory()->withContributors([$member])->create(['created_by' => $owner->id]);
    $member->update(['role' => 'ADMIN']);

    $this->actingAs($owner)
        ->put(route('researches.update', $research), researchPayload(overrides: [
            'contributors' => [['user_id' => $owner->id], ['user_id' => $member->id]],
        ]))
        ->assertSessionHasNoErrors();

    expect($research->contributors()->pluck('user_id')->all())->toBe([$owner->id, $member->id]);
});

it('ignores array input in list filters', function () {
    $this->actingAs(admin());

    $this->get(route('researches.index', ['q' => ['x'], 'year' => ['2024']]))->assertOk();
    $this->get(route('users.index', ['q' => ['x']]))->assertOk();
    $this->get(route('verifications.index', ['q' => ['x'], 'type' => ['research']]))->assertOk();
    $this->get(route('master.faculties.index', ['q' => ['x']]))->assertOk();
    $this->get(route('master.schemes.index', ['q' => ['x'], 'type' => ['RESEARCH']]))->assertOk();
    $this->get(route('master.study-programs.index', ['faculty' => ['1']]))->assertOk();
});
