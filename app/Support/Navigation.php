<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * Single source of the sidebar menu. An item becomes clickable as soon as
 * its route exists — until then it renders disabled ("Segera").
 */
final class Navigation
{
    /**
     * @return list<array{label: ?string, items: list<array{label: string, icon: string, url: ?string, active: bool, badge: ?int}>}>
     */
    public static function for(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $groups = [
            [
                'label' => null,
                'items' => [
                    self::item('Dashboard', 'heroicon-o-home', 'dashboard', 'dashboard'),
                ],
            ],
            [
                'label' => 'Data Kinerja',
                'items' => [
                    self::item('Penelitian', 'heroicon-o-beaker', 'researches.index', 'researches.*'),
                    self::item('Pengabdian', 'heroicon-o-user-group', 'community-services.index', 'community-services.*'),
                    self::item('Publikasi', 'heroicon-o-newspaper', 'publications.index', 'publications.*'),
                    self::item('Buku', 'heroicon-o-book-open', 'books.index', 'books.*'),
                    self::item('HKI', 'heroicon-o-light-bulb', 'intellectual-properties.index', 'intellectual-properties.*'),
                ],
            ],
            [
                'label' => 'Administrasi',
                'roles' => [UserRole::Admin],
                'items' => [
                    self::item('Verifikasi', 'heroicon-o-clipboard-document-check', 'verifications.index', 'verifications.*',
                        badge: fn () => Verifiable::pendingCount()),
                    self::item('Pengguna', 'heroicon-o-users', 'users.index', 'users.*'),
                    self::item('Data Master', 'heroicon-o-circle-stack', 'master.faculties.index', 'master.*'),
                    self::item('Rekap', 'heroicon-o-chart-bar', 'reports.index', 'reports.*'),
                    self::item('Pengaturan', 'heroicon-o-cog-6-tooth', 'settings.edit', 'settings.*'),
                ],
            ],
        ];

        return collect($groups)
            ->filter(fn (array $group) => ! isset($group['roles']) || in_array($user->role, $group['roles'], true))
            ->map(fn (array $group) => [
                'label' => $group['label'],
                'items' => array_map(fn (array $item) => [
                    'label' => $item['label'],
                    'icon' => $item['icon'],
                    'url' => Route::has($item['route']) ? route($item['route']) : null,
                    'active' => request()->routeIs($item['match']),
                    'badge' => Route::has($item['route']) && $item['badge'] ? ($item['badge'])() : null,
                ], $group['items']),
            ])
            ->values()
            ->all();
    }

    private static function item(string $label, string $icon, string $route, string $match, ?\Closure $badge = null): array
    {
        return compact('label', 'icon', 'route', 'match', 'badge');
    }
}
