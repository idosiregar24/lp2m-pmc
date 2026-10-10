<?php

use App\Exports\ActivityExport;
use App\Exports\ReportExport;
use App\Exports\ResearchExport;
use App\Models\Book;
use App\Models\IntellectualProperty;
use App\Models\Publication;
use App\Models\Research;
use Maatwebsite\Excel\Facades\Excel;

it('keeps the recap admin-only', function () {
    $this->get(route('reports.index'))->assertRedirect(route('login'));
    $this->actingAs(dosen())->get(route('reports.index'))->assertForbidden();
    $this->get(route('reports.export'))->assertForbidden();
    $this->actingAs(admin())->get(route('reports.index'))->assertOk();
});

it('counts verified records per lecturer and module', function () {
    $lecturer = dosen(['name' => 'Dr. Rekap Satu']);
    Research::factory()->verified()->withContributors()->count(2)->create(['created_by' => $lecturer->id, 'year' => 2024]);
    Research::factory()->draft()->withContributors()->create(['created_by' => $lecturer->id]);
    Publication::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'year' => 2023]);
    // A member, not the creator, still counts.
    Book::factory()->verified()->withContributors([$lecturer])->create();

    $this->actingAs(admin());

    $rows = $this->get(route('reports.index', ['lecturer' => $lecturer->id]))->assertOk()->viewData('rows');
    expect($rows->first()['counts'])->toMatchArray(['research' => 2, 'publication' => 1, 'book' => 1])
        ->and($rows->first()['total'])->toBe(4);

    $rows = $this->get(route('reports.index', ['lecturer' => $lecturer->id, 'year' => 2024]))->viewData('rows');
    expect($rows->first()['total'])->toBe(2);
});

it('drills down from the recap to the lecturer\'s records', function () {
    $lecturer = dosen();
    Research::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'title' => 'Milik Dosen Rekap']);
    Research::factory()->verified()->withContributors()->create(['title' => 'Milik Dosen Lain']);

    $this->actingAs(admin())->get(route('researches.index', ['lecturer' => $lecturer->id, 'status' => 'VERIFIED']))
        ->assertOk()
        ->assertSee('Milik Dosen Rekap')
        ->assertDontSee('Milik Dosen Lain')
        ->assertSee($lecturer->name);
});

it('exports the recap', function () {
    Excel::fake();

    $this->actingAs(admin())->get(route('reports.export'))->assertOk();

    Excel::assertDownloaded('rekap-dosen-'.now()->format('Ymd-His').'.xlsx', fn (ReportExport $export) => $export->headings()[0] === 'Nama Dosen');
});

it('exports only verified records the user may see by default', function () {
    Excel::fake();
    $me = dosen();
    Research::factory()->verified()->withContributors()->create(['created_by' => $me->id, 'title' => 'Terverifikasi Saya']);
    Research::factory()->draft()->withContributors()->create(['created_by' => $me->id, 'title' => 'Draf Saya']);
    Research::factory()->verified()->withContributors()->create(['title' => 'Punya Orang Lain']);

    $this->actingAs($me)->get(route('researches.export'))->assertOk();

    Excel::assertDownloaded('penelitian-'.now()->format('Ymd-His').'.xlsx', function (ResearchExport $export) {
        $titles = $export->query()->pluck('title')->all();

        return $titles === ['Terverifikasi Saya'];
    });
});

it('follows the status filter when exporting', function () {
    Excel::fake();
    $me = dosen();
    Research::factory()->draft()->withContributors()->create(['created_by' => $me->id, 'title' => 'Draf Saya']);

    $this->actingAs($me)->get(route('researches.export', ['status' => 'DRAFT']));

    Excel::assertDownloaded('penelitian-'.now()->format('Ymd-His').'.xlsx',
        fn (ResearchExport $export) => $export->query()->pluck('title')->all() === ['Draf Saya']);
});

it('maps a row with members and neutralises formulas', function () {
    $lecturer = dosen();
    $research = Research::factory()->verified()->withContributors()->create(['created_by' => $lecturer->id, 'title' => '=HYPERLINK("x")']);
    $research->contributors()->create(['name' => '+Pihak Luar', 'affiliation' => 'Universitas X', 'sort_order' => 2]);

    $export = new ResearchExport(Research::query());
    $row = $export->map($export->query()->first());

    expect($row[0])->toBe('\'=HYPERLINK("x")')
        ->and($row[2])->toBe($research->scheme->name)
        ->and($row[4])->toBe($lecturer->name.'; +Pihak Luar (Universitas X)')
        ->and(end($row))->toBe('Terverifikasi')
        ->and(ActivityExport::safe(5))->toBe(5);
});

it('exports every module', function (string $route) {
    Excel::fake();

    $this->actingAs(admin())->get(route($route.'.export'))->assertOk();
})->with(['researches', 'community-services', 'publications', 'books', 'intellectual-properties']);

it('falls back to verified records for an unknown export status', function () {
    Excel::fake();
    $me = dosen();
    Research::factory()->draft()->withContributors()->create(['created_by' => $me->id]);

    $this->actingAs($me)->get(route('researches.export', ['status' => 'x']));

    Excel::assertDownloaded('penelitian-'.now()->format('Ymd-His').'.xlsx',
        fn (ResearchExport $export) => $export->query()->count() === 0);
});

it('shows the lecturer chip to admins only', function () {
    $other = dosen(['name' => 'Nama Rahasia Dosen']);

    $this->actingAs(dosen())->get(route('books.index', ['lecturer' => $other->id]))->assertOk()->assertDontSee('Nama Rahasia Dosen');
    $this->actingAs(admin())->get(route('books.index', ['lecturer' => $other->id]))->assertSee('Nama Rahasia Dosen');
});

it('resets the HKI type filter', function () {
    IntellectualProperty::factory()->withContributors()->create(['type' => 'PATENT']);

    $this->actingAs(admin())->get(route('intellectual-properties.index', ['type' => 'PATENT']))
        ->assertOk()
        ->assertSee('Reset')
        // Reset must not carry the filter along (it used to be treated as a page tab).
        ->assertDontSee(route('intellectual-properties.index').'?type=PATENT"', false);
});
