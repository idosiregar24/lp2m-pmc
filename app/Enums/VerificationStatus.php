<?php

namespace App\Enums;

/**
 * Verification state machine (PRD §6.4). Transitions are enforced by
 * VerificationService through canTransitionTo().
 */
enum VerificationStatus: string
{
    case Draft = 'DRAFT';
    case Submitted = 'SUBMITTED';
    case NeedsRevision = 'NEEDS_REVISION';
    case Verified = 'VERIFIED';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Submitted => 'Diajukan',
            self::NeedsRevision => 'Perlu Revisi',
            self::Verified => 'Terverifikasi',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Draft => 'neutral',
            self::Submitted => 'info',
            self::NeedsRevision => 'warning',
            self::Verified => 'success',
        };
    }

    /**
     * Owner (Dosen) may still edit, delete and submit.
     */
    public function isEditableByOwner(): bool
    {
        return in_array($this, [self::Draft, self::NeedsRevision], true);
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, match ($this) {
            self::Draft, self::NeedsRevision => [self::Submitted],
            self::Submitted => [self::Verified, self::NeedsRevision],
            self::Verified => [self::NeedsRevision],
        }, true);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
