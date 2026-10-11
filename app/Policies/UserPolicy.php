<?php

namespace App\Policies;

use App\Models\User;

/**
 * Account management is Admin-only (route role:ADMIN); these are the extra guards.
 */
class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function view(User $actor, User $user): bool
    {
        return $actor->isAdmin();
    }

    public function create(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function update(User $actor, User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return $actor->isSuperAdmin();
        }

        return $actor->isAdmin();
    }

    public function manageRole(User $actor, User $user): bool
    {
        return $actor->isSuperAdmin() && ! $actor->is($user);
    }

    /**
     * Not oneself — the system must always keep an active Admin.
     */
    public function toggleActive(User $actor, User $user): bool
    {
        if ($user->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            return false;
        }

        return $actor->isAdmin() && ! $actor->is($user);
    }

    public function resetPassword(User $actor, User $user): bool
    {
        if ($user->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            return false;
        }

        return $actor->isAdmin();
    }

    /**
     * Only accounts without any research data; others are deactivated (PRD §4.2).
     */
    public function delete(User $actor, User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        return $actor->isAdmin() && ! $actor->is($user) && ! $user->hasActivityData();
    }
}
