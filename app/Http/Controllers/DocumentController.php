<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The only way a supporting document leaves the private disk.
 */
class DocumentController extends Controller
{
    public function download(Document $document): StreamedResponse
    {
        $this->authorize('download', $document);

        abort_unless(Storage::disk('local')->exists($document->path), 404);

        return Storage::disk('local')->download($document->path, $document->original_name, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(Document $document, DocumentService $service): RedirectResponse
    {
        $this->authorize('delete', $document);

        $service->delete($document);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
