<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\SaveFundingSourceRequest;
use App\Models\FundingSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FundingSourceController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request, ['q']);

        return view('master.funding-sources.index', [
            'fundingSources' => FundingSource::query()
                ->when($filters['q'] ?? null, fn ($q, $term) => $q->where('name', 'like', '%'.$term.'%'))
                ->orderBy('name')
                ->paginate(config('bp2m.per_page'))
                ->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('master.funding-sources.form', ['fundingSource' => new FundingSource(['is_active' => true])]);
    }

    public function store(SaveFundingSourceRequest $request): RedirectResponse
    {
        FundingSource::create($request->validated());

        return redirect()->route('master.funding-sources.index')->with('success', 'Sumber dana berhasil ditambahkan.');
    }

    public function edit(FundingSource $fundingSource): View
    {
        return view('master.funding-sources.form', ['fundingSource' => $fundingSource]);
    }

    public function update(SaveFundingSourceRequest $request, FundingSource $fundingSource): RedirectResponse
    {
        $fundingSource->update($request->validated());

        return redirect()->route('master.funding-sources.index')->with('success', 'Sumber dana berhasil diperbarui.');
    }

    public function destroy(FundingSource $fundingSource): RedirectResponse
    {
        if ($fundingSource->isInUse()) {
            return back()->with('error', 'Sumber dana sudah dipakai data, nonaktifkan saja.');
        }

        $fundingSource->delete();

        return redirect()->route('master.funding-sources.index')->with('success', 'Sumber dana berhasil dihapus.');
    }
}
