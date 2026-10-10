<?php

namespace App\Models\Concerns;

use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\VerificationLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Verification columns (PRD §4.11). Never fillable — only VerificationService
 * changes status, submitted_at, verified_at, verified_by and revision_note.
 */
trait HasVerification
{
    public function initializeHasVerification(): void
    {
        $this->mergeCasts([
            'status' => VerificationStatus::class,
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ]);

        if (! array_key_exists('status', $this->attributes)) {
            $this->attributes['status'] = VerificationStatus::Draft->value;
        }
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function verificationLogs(): MorphMany
    {
        return $this->morphMany(VerificationLog::class, 'verifiable')->latest('id');
    }

    /**
     * Official numbers (dashboard, recap, export) count verified records only.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), VerificationStatus::Verified);
    }

    public function scopeStatus(Builder $query, VerificationStatus|string|null $status): Builder
    {
        if ($status === null || $status === '') {
            return $query;
        }

        $status = $status instanceof VerificationStatus ? $status : VerificationStatus::tryFrom($status);

        return $status ? $query->where($this->qualifyColumn('status'), $status) : $query;
    }

    /**
     * SUBMITTED / VERIFIED records are locked for their Dosen owner.
     */
    public function isLockedForOwner(): bool
    {
        return ! $this->status->isEditableByOwner();
    }
}
