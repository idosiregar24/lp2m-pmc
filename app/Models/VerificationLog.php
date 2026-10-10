<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * Append-only verification history (PRD §4.11): rows are never updated or deleted.
 */
class VerificationLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['from_status', 'to_status', 'note', 'acted_by'];

    protected function casts(): array
    {
        return [
            'from_status' => VerificationStatus::class,
            'to_status' => VerificationStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Riwayat verifikasi tidak dapat diubah.'));
        static::deleting(fn () => throw new LogicException('Riwayat verifikasi tidak dapat dihapus.'));
    }

    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acted_by');
    }
}
