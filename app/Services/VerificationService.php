<?php

namespace App\Services;

use App\Enums\VerificationStatus;
use App\Exceptions\InvalidStatusTransition;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The only place a record's verification status changes (PRD §4.11, §6.4).
 * Each move locks the row, checks the transition, writes the append-only
 * verification_logs row — all in one transaction.
 */
class VerificationService
{
    public function submit(Model $record, User $actor): void
    {
        if (config('bp2m.verification.require_document') && ! $record->documents()->exists()) {
            throw InvalidStatusTransition::missingDocument();
        }

        $this->transition($record, VerificationStatus::Submitted, $actor, null, function (Model $record) {
            $record->submitted_at = now();
        });
    }

    public function verify(Model $record, User $admin): void
    {
        $this->transition($record, VerificationStatus::Verified, $admin, null, function (Model $record) use ($admin) {
            $record->verified_at = now();
            $record->verified_by = $admin->id;
            $record->revision_note = null;
        });
    }

    public function requestRevision(Model $record, User $admin, string $note): void
    {
        $this->transition($record, VerificationStatus::NeedsRevision, $admin, $note, function (Model $record) use ($note) {
            $record->revision_note = $note;
            $record->verified_at = null;
            $record->verified_by = null;
        });
    }

    /**
     * Admin sends a VERIFIED record back for revision — same target state as requestRevision.
     */
    public function reopen(Model $record, User $admin, string $note): void
    {
        $this->requestRevision($record, $admin, $note);
    }

    /**
     * Records created or edited by an Admin are verified directly (the Admin is the verifier).
     */
    public function markVerifiedByAdmin(Model $record, User $admin): void
    {
        DB::transaction(function () use ($record, $admin) {
            $from = $this->lockedStatus($record);

            if ($from === VerificationStatus::Verified) {
                return;
            }

            $record->forceFill([
                'status' => VerificationStatus::Verified,
                'verified_at' => now(),
                'verified_by' => $admin->id,
                'revision_note' => null,
            ])->save();

            $this->log($record, $from, VerificationStatus::Verified, $admin, 'Diinput/diubah oleh Admin.');
        });
    }

    /**
     * Re-checks the owner's edit right under a row lock, inside the caller's transaction:
     * the Form Request authorised before the transaction, and another tab may have submitted since.
     */
    public function ensureEditableByOwner(Model $record): void
    {
        if (! $this->lockedStatus($record)->isEditableByOwner()) {
            throw InvalidStatusTransition::lockedForOwner();
        }
    }

    private function transition(Model $record, VerificationStatus $to, User $actor, ?string $note, Closure $mutate): void
    {
        DB::transaction(function () use ($record, $to, $actor, $note, $mutate) {
            $from = $this->lockedStatus($record);

            if (! $from->canTransitionTo($to)) {
                throw InvalidStatusTransition::between($from, $to);
            }

            $mutate($record);
            $record->status = $to;
            $record->save();

            $this->log($record, $from, $to, $actor, $note);
        });
    }

    /**
     * Current status read with a row lock, so two Admins cannot act on the same record at once.
     */
    private function lockedStatus(Model $record): VerificationStatus
    {
        $status = $record->newQueryWithoutScopes()
            ->whereKey($record->getKey())
            ->lockForUpdate()
            ->value('status');

        // Eloquent's value() applies the model cast, so this is usually already an enum.
        return $status instanceof VerificationStatus ? $status : VerificationStatus::from($status);
    }

    private function log(Model $record, ?VerificationStatus $from, VerificationStatus $to, User $actor, ?string $note): void
    {
        $record->verificationLogs()->create([
            'from_status' => $from,
            'to_status' => $to,
            'note' => $note,
            'acted_by' => $actor->id,
        ]);
        $record->unsetRelation('verificationLogs');
    }
}
