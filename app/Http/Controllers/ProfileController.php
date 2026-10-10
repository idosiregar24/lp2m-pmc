<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user()->load('studyProgram.faculty'),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->forceFill(['password' => $request->validated('password')])->save();

        return back()->with('success', 'Password berhasil diganti.');
    }
}
