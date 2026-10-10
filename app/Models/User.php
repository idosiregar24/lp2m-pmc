<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\Verifiable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Only Admin forms (validated by Form Requests) write role/is_active.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nidn_nuptk',
        'study_program_id',
        'username',
        'password',
        'role',
        'is_active',
        'must_change_password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contributor::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isDosen(): bool
    {
        return $this->role === UserRole::Dosen;
    }

    public function initials(): string
    {
        // "Dr. Budi Santoso, M.Kom." → "BS": drop degrees after the comma and titles ending in "."
        return Str::of($this->name)
            ->before(',')
            ->explode(' ')
            ->filter(fn (string $word) => $word !== '' && ! str_ends_with($word, '.'))
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('') ?: 'U';
    }

    /**
     * Active lecturers, for contributor pickers.
     */
    public function scopeLecturers(Builder $query): Builder
    {
        return $query->where('role', UserRole::Dosen)->where('is_active', true)->orderBy('name');
    }

    /**
     * Tom Select options: [['id' => '1', 'label' => 'Nama (NIDN)'], …]. Users in
     * $alwaysInclude (e.g. contributors already on a record) are kept even if inactive.
     *
     * @param  array<int>  $alwaysInclude
     * @return list<array{id: string, label: string}>
     */
    public static function lecturerOptions(array $alwaysInclude = []): array
    {
        return self::query()
            // Current members stay pickable even if since deactivated or made Admin.
            ->where(fn (Builder $q) => $q
                ->where(fn (Builder $q) => $q->where('role', UserRole::Dosen)->where('is_active', true))
                ->orWhereIn('id', $alwaysInclude))
            ->orderBy('name')
            ->get(['id', 'name', 'nidn_nuptk'])
            ->map(fn (self $user) => [
                'id' => (string) $user->id,
                'label' => $user->nidn_nuptk ? "{$user->name} ({$user->nidn_nuptk})" : $user->name,
            ])
            ->all();
    }

    /**
     * True when the account appears in any research data — such accounts are
     * deactivated instead of deleted (PRD §4.2).
     */
    public function hasActivityData(): bool
    {
        if ($this->contributions()->exists()) {
            return true;
        }

        foreach (Verifiable::modelClasses() as $model) {
            $used = $model::withTrashed()
                ->where(fn (Builder $q) => $q->where('created_by', $this->id)
                    ->orWhere('updated_by', $this->id)
                    ->orWhere('verified_by', $this->id))
                ->exists();

            if ($used) {
                return true;
            }
        }

        return Document::where('uploaded_by', $this->id)->exists()
            || VerificationLog::where('acted_by', $this->id)->exists();
    }
}
