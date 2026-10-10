<?php

namespace App\Http\Controllers;

use App\Services\VerificationService;
use App\Support\Verifiable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * A Dosen submits a record for verification ("Ajukan") — any module, via its morph alias.
 */
class SubmissionController extends Controller
{
    public function __invoke(Request $request, string $type, int $id, VerificationService $verification): RedirectResponse
    {
        $record = Verifiable::resolve($type, $id);

        $this->authorize('submit', $record);

        $verification->submit($record, $request->user());

        return back()->with('success', 'Data diajukan untuk verifikasi admin BP2M.');
    }
}
