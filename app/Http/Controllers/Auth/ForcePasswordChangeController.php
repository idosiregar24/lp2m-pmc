<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForcePasswordChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Replaces a temporary password set by an Admin (users.must_change_password).
 */
class ForcePasswordChangeController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        if (! $request->user()->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.force-password');
    }

    public function update(ForcePasswordChangeRequest $request): RedirectResponse
    {
        $request->user()->forceFill([
            'password' => $request->validated('password'),
            'must_change_password' => false,
        ])->save();

        return redirect()->route('dashboard')->with('success', 'Password berhasil diganti.');
    }
}
