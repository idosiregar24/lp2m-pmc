<?php

namespace Database\Seeders;

use App\Enums\SchemeType;
use App\Enums\UserRole;
use App\Models\Book;
use App\Models\CommunityService;
use App\Models\FundingSource;
use App\Models\IntellectualProperty;
use App\Models\Publication;
use App\Models\Research;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Demo data for the five modules in every verification status (no real files on disk).
 */
class DemoDataSeeder extends Seeder
{
    private const STATES = ['draft', 'submitted', 'needsRevision', 'verified', 'verified'];

    public function run(): void
    {
        $sources = FundingSource::query()->pluck('id');
        $researchSchemes = Scheme::query()->where('type', SchemeType::Research)->pluck('id');
        $serviceSchemes = Scheme::query()->where('type', SchemeType::CommunityService)->pluck('id');

        $this->seedModule(Research::class, 20, fn () => [
            'scheme_id' => $researchSchemes->random(),
            'funding_source_id' => $sources->random(),
        ]);
        $this->seedModule(CommunityService::class, 20, fn () => [
            'scheme_id' => $serviceSchemes->random(),
            'funding_source_id' => $sources->random(),
        ]);
        $this->seedModule(Publication::class, 20);
        $this->seedModule(Book::class, 10);
        $this->seedModule(IntellectualProperty::class, 10);
    }

    /**
     * @param  class-string<Model>  $model
     * @param  (callable(): array<string, mixed>)|null  $attributes  module-specific columns
     */
    private function seedModule(string $model, int $count, ?callable $attributes = null): void
    {
        $lecturers = User::query()->where('role', UserRole::Dosen)->get();
        if ($lecturers->isEmpty()) {
            return;
        }

        $admin = User::query()->whereIn('role', [UserRole::SuperAdmin, UserRole::Admin])->first();

        foreach (range(1, $count) as $i) {
            // The demo "dosen" account owns the first few, so its pages are not empty.
            $creator = $i <= 4 ? $lecturers->firstWhere('username', 'dosen') : $lecturers->random();
            $state = self::STATES[$i % count(self::STATES)];

            $record = $model::factory()
                ->{$state}()
                ->withContributors($lecturers->except($creator->id)->random(rand(0, 2))->all())
                ->withDocument()
                ->create([
                    'created_by' => $creator->id,
                    'verified_by' => $state === 'verified' ? $admin->id : null,
                    ...($attributes ? $attributes() : []),
                ]);

            if ($state !== 'draft') {
                $record->verificationLogs()->create([
                    'from_status' => 'DRAFT',
                    'to_status' => 'SUBMITTED',
                    'acted_by' => $creator->id,
                ]);
            }
        }
    }
}
