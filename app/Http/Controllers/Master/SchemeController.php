<?php

namespace App\Http\Controllers\Master;

use App\Enums\SchemeType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\SaveSchemeRequest;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchemeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request, ['q', 'type']);

        return view('master.schemes.index', [
            'schemes' => Scheme::query()
                ->when($filters['q'] ?? null, fn ($q, $term) => $q->where('name', 'like', '%'.$term.'%'))
                ->when(SchemeType::tryFrom($filters['type'] ?? ''), fn ($q, $type) => $q->where('type', $type))
                ->orderBy('type')->orderBy('name')
                ->paginate(config('bp2m.per_page'))
                ->withQueryString(),
            'filters' => $filters,
            'types' => SchemeType::options(),
        ]);
    }

    public function create(): View
    {
        return view('master.schemes.form', [
            'scheme' => new Scheme(['is_active' => true]),
            'types' => SchemeType::options(),
        ]);
    }

    public function store(SaveSchemeRequest $request): RedirectResponse
    {
        Scheme::create($request->validated());

        return redirect()->route('master.schemes.index')->with('success', 'Skema berhasil ditambahkan.');
    }

    public function edit(Scheme $scheme): View
    {
        return view('master.schemes.form', [
            'scheme' => $scheme,
            'types' => SchemeType::options(),
        ]);
    }

    public function update(SaveSchemeRequest $request, Scheme $scheme): RedirectResponse
    {
        if ($scheme->type->value !== $request->validated('type') && $scheme->isInUse()) {
            return back()->withInput()->with('error', 'Jenis skema yang sudah dipakai data tidak dapat diubah.');
        }

        $scheme->update($request->validated());

        return redirect()->route('master.schemes.index')->with('success', 'Skema berhasil diperbarui.');
    }

    public function destroy(Scheme $scheme): RedirectResponse
    {
        if ($scheme->isInUse()) {
            return back()->with('error', 'Skema sudah dipakai data, nonaktifkan saja.');
        }

        $scheme->delete();

        return redirect()->route('master.schemes.index')->with('success', 'Skema berhasil dihapus.');
    }
}
