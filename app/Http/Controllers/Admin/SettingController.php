<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Services\SettingService;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Pengaturan Sistem (PRD §4.12) — university logo.
 */
class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', ['logoUrl' => Settings::logoUrl()]);
    }

    public function update(UpdateSettingRequest $request, SettingService $service): RedirectResponse
    {
        if ($request->boolean('remove_logo')) {
            $service->removeLogo();

            return back()->with('success', 'Logo dihapus. Sistem kembali memakai lambang sementara.');
        }

        $service->updateLogo($request->file('logo'));

        return back()->with('success', 'Logo berhasil diperbarui.');
    }
}
