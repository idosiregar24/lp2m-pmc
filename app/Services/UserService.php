<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Account management by an Admin (PRD §4.2). Passwords set here are temporary.
 */
class UserService
{
    /**
     * @param  array<string, mixed>  $data  validated input
     */
    public function create(array $data): User
    {
        $user = new User($this->attributes($data));
        $user->password = $data['password'];
        $user->must_change_password = true;
        $user->save();

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data  validated input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $data, User $actor): User
    {
        $attributes = $this->attributes($data);

        if ($user->is($actor)) {
            $this->guardSelf($actor, $attributes);
        } elseif (isset($attributes['role']) && $attributes['role'] !== $user->role->value) {
            if (! $actor->isSuperAdmin()) {
                throw ValidationException::withMessages(['role' => 'Hanya Super Admin yang dapat mengubah role pengguna.']);
            }
        }

        $user->fill($attributes);

        $passwordChanged = filled($data['password'] ?? null);

        if ($passwordChanged) {
            $user->password = $data['password'];
            $user->must_change_password = true;
        }

        $user->save();

        if ($passwordChanged && ! $user->is($actor)) {
            $this->logoutEverywhere($user);
        }

        return $user;
    }

    /**
     * @throws ValidationException
     */
    public function toggleActive(User $user, User $actor): void
    {
        if ($user->is($actor)) {
            throw ValidationException::withMessages(['is_active' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $user->forceFill(['is_active' => ! $user->is_active])->save();
    }

    /**
     * Generates a temporary password; returns it so the Admin can pass it on once.
     */
    public function resetPassword(User $user): string
    {
        $password = Str::password(10, symbols: false);

        $user->forceFill([
            'password' => $password,
            'must_change_password' => true,
        ])->save();

        $this->logoutEverywhere($user);

        return $password;
    }

    /**
     * Ends every session and "remember me" cookie of the account, so whoever held it
     * before the reset cannot pick the new password on ganti-password.
     */
    private function logoutEverywhere(User $user): void
    {
        DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();

        $user->forceFill(['remember_token' => Str::random(60)])->save();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data): array
    {
        $attributes = Arr::only($data, ['name', 'nidn_nuptk', 'study_program_id', 'username', 'role', 'is_active']);
        $attributes['nidn_nuptk'] = ($attributes['nidn_nuptk'] ?? null) ?: null;
        $attributes['study_program_id'] = ($attributes['study_program_id'] ?? null) ?: null;

        return $attributes;
    }

    /**
     * An Admin cannot demote or deactivate themself (the system must keep an active Admin).
     *
     * @param  array<string, mixed>  $attributes
     */
    private function guardSelf(User $actor, array $attributes): void
    {
        if (isset($attributes['role']) && $attributes['role'] !== $actor->role->value) {
            throw ValidationException::withMessages(['role' => 'Anda tidak dapat mengubah role akun Anda sendiri.']);
        }

        if (array_key_exists('is_active', $attributes) && ! $attributes['is_active']) {
            throw ValidationException::withMessages(['is_active' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }
    }
}
