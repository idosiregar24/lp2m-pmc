<?php

use App\Support\Settings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

it('lets only admins open the settings page', function () {
    $this->actingAs(admin())->get(route('settings.edit'))->assertOk();
    $this->actingAs(dosen())->get(route('settings.edit'))->assertForbidden();
});

it('uploads a logo, replaces the old one and shows it in the layout', function () {
    $this->actingAs(admin())
        ->put(route('settings.update'), ['logo' => UploadedFile::fake()->image('logo.png', 256, 256)])
        ->assertSessionHasNoErrors();

    $first = Settings::get(Settings::LOGO_PATH);
    Storage::disk('public')->assertExists($first);

    $this->put(route('settings.update'), ['logo' => UploadedFile::fake()->image('baru.png', 256, 256)]);

    $second = Settings::get(Settings::LOGO_PATH);
    Storage::disk('public')->assertMissing($first);
    Storage::disk('public')->assertExists($second);

    $this->get(route('login'))->assertDontSee('PMC</span>', false);
});

it('refuses SVG, oversized and tiny logos', function (UploadedFile $file) {
    $this->actingAs(admin())->put(route('settings.update'), ['logo' => $file])->assertSessionHasErrors('logo');
})->with([
    'svg' => fn () => UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'),
    'terlalu besar' => fn () => UploadedFile::fake()->image('logo.png', 512, 512)->size(2048),
    'terlalu kecil' => fn () => UploadedFile::fake()->image('logo.png', 64, 64),
]);

it('removes the logo', function () {
    $this->actingAs(admin())->put(route('settings.update'), ['logo' => UploadedFile::fake()->image('logo.png', 256, 256)]);
    $path = Settings::get(Settings::LOGO_PATH);

    $this->put(route('settings.update'), ['remove_logo' => '1'])->assertSessionHasNoErrors();

    expect(Settings::logoUrl())->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
