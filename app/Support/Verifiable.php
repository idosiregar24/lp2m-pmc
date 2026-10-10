<?php

namespace App\Support;

use App\Enums\VerificationStatus;
use App\Models\Book;
use App\Models\CommunityService;
use App\Models\IntellectualProperty;
use App\Models\Publication;
use App\Models\Research;
use Illuminate\Database\Eloquent\Model;

/**
 * Registry of the research-data modules (PRD §4.4–4.8): morph alias → model,
 * label and route prefix. The morph map, the verification routes
 * ({type}/{id}) and the Verifikasi queue all read this one list — a new
 * module is one entry here.
 */
final class Verifiable
{
    /**
     * `public` = relations publicSummary() reads (public lecturer page).
     *
     * @var array<string, array{model: class-string<Model>, label: string, route: string, icon: string, public: list<string>}>
     */
    public const MODULES = [
        'research' => ['model' => Research::class, 'label' => 'Penelitian', 'route' => 'researches', 'icon' => 'heroicon-o-beaker', 'public' => ['scheme', 'fundingSource']],
        'community_service' => ['model' => CommunityService::class, 'label' => 'Pengabdian', 'route' => 'community-services', 'icon' => 'heroicon-o-user-group', 'public' => ['scheme']],
        'publication' => ['model' => Publication::class, 'label' => 'Publikasi', 'route' => 'publications', 'icon' => 'heroicon-o-newspaper', 'public' => []],
        'book' => ['model' => Book::class, 'label' => 'Buku', 'route' => 'books', 'icon' => 'heroicon-o-book-open', 'public' => []],
        'intellectual_property' => ['model' => IntellectualProperty::class, 'label' => 'HKI', 'route' => 'intellectual-properties', 'icon' => 'heroicon-o-light-bulb', 'public' => []],
    ];

    /** @return array<string, class-string<Model>> */
    public static function morphMap(): array
    {
        return array_map(fn (array $module) => $module['model'], self::MODULES);
    }

    /** @return list<class-string<Model>> */
    public static function modelClasses(): array
    {
        return array_values(self::morphMap());
    }

    public static function exists(string $type): bool
    {
        return isset(self::MODULES[$type]);
    }

    /**
     * Resolve a whitelisted alias + id to its record; unknown alias → 404.
     */
    public static function resolve(string $type, int|string $id): Model
    {
        abort_unless(self::exists($type), 404);

        return self::MODULES[$type]['model']::query()->findOrFail($id);
    }

    public static function label(string $type): string
    {
        return self::MODULES[$type]['label'] ?? $type;
    }

    public static function showUrl(Model $record): string
    {
        return route(self::MODULES[$record->getMorphClass()]['route'].'.show', $record);
    }

    /**
     * Records waiting for an Admin across all modules (sidebar badge).
     */
    public static function pendingCount(): int
    {
        return collect(self::modelClasses())
            ->sum(fn (string $model) => $model::query()->where('status', VerificationStatus::Submitted)->count());
    }
}
