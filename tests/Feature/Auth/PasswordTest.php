<?php

use Illuminate\Support\Facades\Hash;

it('forces a temporary password to be changed first', function () {
    $user = dosen(['must_change_password' => true]);

    $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('password.force'));
    $this->get(route('password.force'))->assertOk();

    $this->put(route('password.force'), ['password' => 'rahasia-baru', 'password_confirmation' => 'rahasia-baru'])
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->must_change_password)->toBeFalse()
        ->and(Hash::check('rahasia-baru', $user->password))->toBeTrue();

    $this->get(route('dashboard'))->assertOk();
});

it('skips the forced page when no change is required', function () {
    $this->actingAs(dosen())->get(route('password.force'))->assertRedirect(route('dashboard'));
});

it('shows the profile page', function () {
    $user = dosen();

    $this->actingAs($user)->get(route('profile.show'))->assertOk()->assertSee($user->nidn_nuptk);
});

it('changes the password from the profile with the current password', function () {
    $user = dosen();

    $this->actingAs($user)
        ->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'rahasia-baru',
            'password_confirmation' => 'rahasia-baru',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check('rahasia-baru', $user->fresh()->password))->toBeTrue();
});

it('rejects a wrong current password', function () {
    $this->actingAs(dosen())
        ->put(route('profile.password'), [
            'current_password' => 'salah',
            'password' => 'rahasia-baru',
            'password_confirmation' => 'rahasia-baru',
        ])
        ->assertSessionHasErrors('current_password');
});

it('refuses the forced change without a temporary password', function () {
    $user = dosen();

    $this->actingAs($user)
        ->put(route('password.force'), ['password' => 'rahasia-baru', 'password_confirmation' => 'rahasia-baru'])
        ->assertForbidden();

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});
