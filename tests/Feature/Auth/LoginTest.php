<?php

it('shows the login page to guests', function () {
    $this->get(route('login'))->assertOk()->assertSee('Username');
});

it('redirects guests from the dashboard to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('logs in with username and password', function () {
    $user = dosen(['username' => 'budi']);

    $this->post(route('login'), ['username' => 'BUDI', 'password' => 'password'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->last_login_at)->not->toBeNull();
});

it('rejects a wrong password with a generic message', function () {
    dosen(['username' => 'budi']);

    $this->from(route('login'))
        ->post(route('login'), ['username' => 'budi', 'password' => 'salah'])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['username' => 'Username atau password salah.']);

    $this->assertGuest();
});

it('uses the same message for an unknown username', function () {
    $this->post(route('login'), ['username' => 'tidakada', 'password' => 'salah'])
        ->assertSessionHasErrors(['username' => 'Username atau password salah.']);
});

it('refuses inactive accounts', function () {
    dosen(['username' => 'budi', 'is_active' => false]);

    $this->post(route('login'), ['username' => 'budi', 'password' => 'password'])
        ->assertSessionHasErrors(['username' => 'Akun Anda dinonaktifkan. Hubungi admin BP2M.']);

    $this->assertGuest();
});

it('throttles after five attempts', function () {
    dosen(['username' => 'budi']);

    foreach (range(1, 5) as $ignored) {
        $this->post(route('login'), ['username' => 'budi', 'password' => 'salah']);
    }

    $this->post(route('login'), ['username' => 'budi', 'password' => 'password'])
        ->assertSessionHasErrors('username');

    $this->assertGuest();
});

it('logs out a user deactivated while signed in', function () {
    $user = dosen();
    $this->actingAs($user);
    $user->update(['is_active' => false]);

    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->assertGuest();
});

it('logs out', function () {
    $this->actingAs(dosen())->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();
});

it('blocks lecturers from admin routes and lets admins in', function () {
    $this->actingAs(dosen())->get(route('users.index'))->assertForbidden();
    $this->actingAs(admin())->get(route('users.index'))->assertOk();
});

it('adds a noindex header', function () {
    $this->get(route('login'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

it('renders the dashboard for both roles', function (string $role) {
    $user = $role === 'admin' ? admin() : dosen();

    $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee($user->name);
})->with(['admin', 'dosen']);

it('throttles whitespace and case variants of a username together', function () {
    dosen(['username' => 'budi']);

    foreach (['budi', ' budi', 'budi  ', 'BUDI', ' Budi '] as $variant) {
        $this->post(route('login'), ['username' => $variant, 'password' => 'salah']);
    }

    $this->post(route('login'), ['username' => 'budi', 'password' => 'password'])
        ->assertSessionHasErrors('username');

    $this->assertGuest();
});
