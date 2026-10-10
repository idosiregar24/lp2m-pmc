<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends StoreUserRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->targetUser());
    }

    /**
     * Empty password = unchanged.
     *
     * @return list<mixed>
     */
    protected function passwordRules(): array
    {
        return ['nullable', 'string', Password::defaults(), 'confirmed'];
    }

    protected function targetUser(): ?User
    {
        return $this->route('user');
    }
}
