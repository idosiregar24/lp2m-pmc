<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * List filters from the query string. Only non-empty strings are kept: `?q[]=x`
     * is dropped instead of reaching trim()/where() and failing with a 500.
     *
     * @param  list<string>  $keys
     * @return array<string, string>
     */
    protected function filters(Request $request, array $keys): array
    {
        return array_filter($request->only($keys), fn ($value) => is_string($value) && $value !== '');
    }

    /**
     * The lecturer of a `lecturer` list filter (Rekap drill-down), shown as a chip.
     * Admin only and DOSEN accounts only, so the id cannot be used to look up names.
     *
     * @param  array<string, string>  $filters
     */
    protected function lecturerFilter(Request $request, array $filters): ?User
    {
        if (! isset($filters['lecturer']) || ! $request->user()->isAdmin()) {
            return null;
        }

        return User::query()->where('role', UserRole::Dosen)->find($filters['lecturer']);
    }
}
