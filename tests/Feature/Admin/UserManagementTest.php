<?php

use App\Enums\UserRole;
use App\Models\Research;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function userPayload(array $overrides = []): array
{
    $program = StudyProgram::factory()->create();

    return array_merge([
        'name' => 'Dr. Andi Pratama, M.Kes.',
        'role' => UserRole::Dosen->value,
        'nidn_nuptk' => '1234567890',
        'faculty_id' => $program->faculty_id,
        'study_program_id' => $program->id,
        'username' => 'andi.pratama',
        'password' => 'sementara123',
        'password_confirmation' => 'sementara123',
        'is_active' => '1',
    ], $overrides);
}

it('lists, shows and filters users for admins only', function () {
    $lecturer = dosen(['name' => 'Rina Kartika']);

    $this->actingAs(admin())->get(route('users.index', ['q' => 'Rina']))->assertOk()->assertSee('Rina Kartika');
    $this->get(route('users.show', $lecturer))->assertOk();
    $this->get(route('users.create'))->assertOk();
    $this->get(route('users.edit', $lecturer))->assertOk();

    $this->actingAs(dosen())->get(route('users.create'))->assertForbidden();
});

it('creates a lecturer with a temporary password', function () {
    $this->actingAs(admin())->post(route('users.store'), userPayload())->assertSessionHasNoErrors();

    $user = User::where('username', 'andi.pratama')->firstOrFail();
    expect($user->role)->toBe(UserRole::Dosen)
        ->and($user->must_change_password)->toBeTrue()
        ->and(Hash::check('sementara123', $user->password))->toBeTrue();
});

it('validates lecturer fields', function (array $override, string $field) {
    $this->actingAs(admin())->post(route('users.store'), userPayload($override))->assertSessionHasErrors($field);
})->with([
    'nidn wajib untuk dosen' => [['nidn_nuptk' => ''], 'nidn_nuptk'],
    'nidn bukan 10/16 digit' => [['nidn_nuptk' => '12345'], 'nidn_nuptk'],
    'username format' => [['username' => '1abc'], 'username'],
    'prodi wajib untuk dosen' => [['study_program_id' => ''], 'study_program_id'],
]);

it('rejects a prodi outside the chosen faculty', function () {
    $other = StudyProgram::factory()->create();

    $this->actingAs(admin())
        ->post(route('users.store'), userPayload(['study_program_id' => $other->id]))
        ->assertSessionHasErrors('study_program_id');
});

it('creates an admin without NIDN or prodi', function () {
    $this->actingAs(admin())
        ->post(route('users.store'), userPayload([
            'role' => UserRole::Admin->value, 'nidn_nuptk' => '', 'faculty_id' => '', 'study_program_id' => '', 'username' => 'staf.bp2m',
        ]))
        ->assertSessionHasNoErrors();

    expect(User::where('username', 'staf.bp2m')->first()->isAdmin())->toBeTrue();
});

it('updates a user and keeps the password when left empty', function () {
    $user = dosen();
    $hash = $user->password;
    $payload = userPayload(['username' => $user->username, 'name' => 'Nama Baru', 'password' => '', 'password_confirmation' => '']);

    $this->actingAs(admin())->put(route('users.update', $user), $payload)->assertSessionHasNoErrors();

    expect($user->fresh())->name->toBe('Nama Baru')->password->toBe($hash);
});

it('does not let an admin demote or deactivate themself', function () {
    $me = admin();

    $this->actingAs($me)
        ->put(route('users.update', $me), userPayload(['username' => $me->username, 'nidn_nuptk' => '0987654321', 'password' => '', 'password_confirmation' => '']))
        ->assertSessionHasErrors('role');

    $this->patch(route('users.toggle-active', $me))->assertForbidden();
    expect($me->fresh()->is_active)->toBeTrue();
});

it('deactivates and reactivates another account', function () {
    $user = dosen();

    $this->actingAs(admin())->patch(route('users.toggle-active', $user));
    expect($user->fresh()->is_active)->toBeFalse();

    $this->patch(route('users.toggle-active', $user));
    expect($user->fresh()->is_active)->toBeTrue();
});

it('resets a password to a temporary one shown once', function () {
    $user = dosen();

    $this->actingAs(admin())
        ->patch(route('users.reset-password', $user))
        ->assertSessionHas('generated_password');

    expect($user->fresh()->must_change_password)->toBeTrue();
});

it('deletes only accounts without data', function () {
    $admin = admin();
    $empty = dosen();
    $withData = dosen();
    Research::factory()->withContributors()->create(['created_by' => $withData->id]);

    $this->actingAs($admin)->delete(route('users.destroy', $withData))->assertSessionHas('error');
    expect(User::find($withData->id))->not->toBeNull();

    $this->delete(route('users.destroy', $empty))->assertRedirect(route('users.index'));
    expect(User::find($empty->id))->toBeNull();
});

it('ends the sessions of an account whose password is reset', function () {
    $user = dosen(['remember_token' => 'token-lama']);
    DB::table('sessions')->insert([
        'id' => 'sesi-lama', 'user_id' => $user->id, 'payload' => '', 'last_activity' => now()->timestamp,
    ]);

    $this->actingAs(admin())->patch(route('users.reset-password', $user));

    expect(DB::table('sessions')->where('user_id', $user->id)->exists())->toBeFalse()
        ->and($user->fresh()->remember_token)->not->toBe('token-lama');
});
