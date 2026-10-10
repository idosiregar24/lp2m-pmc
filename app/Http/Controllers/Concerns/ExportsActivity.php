<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\VerificationStatus;
use App\Exports\ActivityExport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * "Ekspor Excel" of a module list: same visibleTo() + filters as the list page
 * (the using controller's FILTERS), official data (Terverifikasi) unless the
 * list was filtered by status (PRD §4.10).
 */
trait ExportsActivity
{
    /**
     * @param  class-string<Model>  $model
     * @param  class-string<ActivityExport>  $export
     */
    protected function exportActivity(Request $request, string $model, string $export, string $fileName): BinaryFileResponse
    {
        $this->authorize('viewAny', $model);

        $filters = $this->filters($request, self::FILTERS);
        // An unknown status would match everything — fall back to VERIFIED instead.
        $filters['status'] = VerificationStatus::tryFrom($filters['status'] ?? '')?->value
            ?? VerificationStatus::Verified->value;

        $query = $model::query()
            ->visibleTo($request->user())
            ->filter($filters)
            ->latest('year')->latest('id');

        return Excel::download(new $export($query), $fileName.'-'.now()->format('Ymd-His').'.xlsx');
    }
}
