<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ForcePasswordChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Without the current password this route is only for a temporary password.
        return (bool) $this->user()?->must_change_password;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['password' => 'password baru'];
    }
}
