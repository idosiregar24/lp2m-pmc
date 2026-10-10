<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['username' => mb_strtolower(trim((string) $this->input('username')))]);
    }

    /**
     * Attempt the login. Failure messages stay generic (PRD §4.1); an inactive
     * account is refused only after the password matched.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages(['username' => __('auth.failed')]);
        }

        if (! Auth::user()->is_active) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages(['username' => __('auth.inactive')]);
        }
    }
}
