<?php

namespace App\Exceptions;

use App\Enums\VerificationStatus;
use RuntimeException;

/**
 * Refused verification status change (PRD §6.4). Rendered as a flash error.
 */
class InvalidStatusTransition extends RuntimeException
{
    public static function between(VerificationStatus $from, VerificationStatus $to): self
    {
        return new self("Status data tidak dapat diubah dari \"{$from->label()}\" menjadi \"{$to->label()}\".");
    }

    public static function lockedForOwner(): self
    {
        return new self('Data sudah diajukan atau terverifikasi sehingga tidak dapat diubah. Hubungi admin BP2M bila perlu perubahan.');
    }

    public static function missingDocument(): self
    {
        return new self('Lampirkan minimal 1 dokumen pendukung sebelum mengajukan verifikasi.');
    }
}
