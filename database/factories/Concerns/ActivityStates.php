<?php

namespace Database\Factories\Concerns;

use App\Enums\VerificationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Contributor, document and verification-status states shared by the
 * factories of the five research-data modules.
 */
trait ActivityStates
{
    /**
     * Adds the creator as lead contributor (sort_order 1) plus optional extra lecturers.
     *
     * @param  array<User>  $members
     */
    public function withContributors(array $members = []): static
    {
        return $this->afterCreating(function (Model $record) use ($members) {
            $record->contributors()->create(['user_id' => $record->created_by, 'sort_order' => 1]);

            foreach (array_values($members) as $index => $member) {
                $record->contributors()->create(['user_id' => $member->id, 'sort_order' => $index + 2]);
            }
        });
    }

    /**
     * A document row without a real file (demo data).
     */
    public function withDocument(): static
    {
        return $this->afterCreating(function (Model $record) {
            $record->documents()->create([
                'original_name' => 'dokumen-pendukung.pdf',
                'path' => "documents/{$record->getMorphClass()}/{$record->id}/dummy.pdf",
                'mime_type' => 'application/pdf',
                'size' => 120_000,
                'uploaded_by' => $record->created_by,
            ]);
        });
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => VerificationStatus::Draft]);
    }

    public function submitted(): static
    {
        return $this->state(fn () => ['status' => VerificationStatus::Submitted, 'submitted_at' => now()]);
    }

    public function needsRevision(): static
    {
        return $this->state(fn () => [
            'status' => VerificationStatus::NeedsRevision,
            'submitted_at' => now()->subDays(3),
            'revision_note' => 'Lampirkan dokumen pendukung yang sudah ditandatangani.',
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'status' => VerificationStatus::Verified,
            'submitted_at' => now()->subDays(7),
            'verified_at' => now()->subDays(2),
        ]);
    }
}
