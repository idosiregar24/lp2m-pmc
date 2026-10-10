<?php

namespace App\Policies;

use App\Enums\VerificationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared rules for the five research-data modules (PRD §7, §4.11).
 * Ownership itself lives in HasContributors (isVisibleTo / isLedOrCreatedBy).
 */
abstract class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Model $record): bool
    {
        return $record->isVisibleTo($user);
    }

    /**
     * Admin always; a Dosen only as creator/lead and only while Draf / Perlu Revisi.
     */
    public function update(User $user, Model $record): bool
    {
        return $user->isAdmin()
            || ($record->isLedOrCreatedBy($user) && ! $record->isLockedForOwner());
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->update($user, $record);
    }

    /**
     * Only Dosen submit — Admin data is verified directly.
     */
    public function submit(User $user, Model $record): bool
    {
        return $user->isDosen()
            && $record->status->canTransitionTo(VerificationStatus::Submitted)
            && $record->isLedOrCreatedBy($user);
    }

    public function approve(User $user, Model $record): bool
    {
        return $user->isAdmin() && $record->status === VerificationStatus::Submitted;
    }

    public function requestRevision(User $user, Model $record): bool
    {
        return $user->isAdmin() && $record->status === VerificationStatus::Submitted;
    }

    public function reopen(User $user, Model $record): bool
    {
        return $user->isAdmin() && $record->status === VerificationStatus::Verified;
    }

    public function restore(User $user, Model $record): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return $user->isAdmin();
    }
}
