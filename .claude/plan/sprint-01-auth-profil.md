# Sprint 01 — Autentikasi & Profil

PRD §2, §4.1, §9. Aturan: `rules/security-standards.md` §1.

## Database & model

- [x] Migrasi `users` sesuai PRD §5.2 (tanpa `email`): `name`,
      `nidn_nuptk` NULL UNIQUE, `study_program_id` (`unsignedBigInteger`
      NULL tanpa FK — constraint ditambahkan migrasi Sprint 02 setelah
      tabel prodi ada), `username` UNIQUE, `password`, `role`, `is_active`,
      `must_change_password`, `last_login_at`, `remember_token`.
- [x] Enum `UserRole` (Admin/Dosen, `label()`, `tone()`).
- [x] Model `User`: `$fillable`, `casts()` (`role`, `is_active`,
      `must_change_password`, `last_login_at`, `password` → `hashed`),
      helper `isAdmin()`, `isDosen()`.
- [x] `UserFactory` (+ state `admin()`, `inactive()`, `mustChangePassword()`).
- [x] `UserSeeder`: `admin`/`password` (ADMIN), `dosen`/`password` (DOSEN).

## Login & sesi

- [x] `LoginController` (create/store/destroy) + `Auth\LoginRequest`
      (username, password, remember).
- [x] `RateLimiter::for('login')` 5/menit per `username|ip`; pesan generik.
- [x] Tolak akun nonaktif dengan pesan PRD §4.1; perbarui `last_login_at`.
- [x] Halaman `auth/login.blade.php` (`<x-layouts.guest>`): Username,
      Password, "Ingat saya", tombol Masuk.
- [x] Middleware `EnsureRole` (`role`), `EnsureUserIsActive` (`active`),
      `EnsurePasswordChanged` (`password.changed`) — alias di
      `bootstrap/app.php`; redirect tamu ke `login`.
- [x] `ForcePasswordChangeController` + halaman `auth/force-password`
      (password baru + konfirmasi).
- [x] Logout (POST, invalidate session).

## Profil Saya

- [x] `ProfileController@show`: data diri hanya-baca (nama, NIDN/NUPTK,
      fakultas, prodi, username, role).
- [x] Ganti password: `current_password` + password baru + konfirmasi
      (`Profile\UpdatePasswordRequest`).
- [x] Menu pengguna di topbar → Profil Saya, Keluar.

## Test

- [x] Login sukses (username), gagal (pesan generik), throttle setelah 5x.
- [x] Akun nonaktif ditolak login & sesi aktif diputus.
- [x] `must_change_password` memaksa ke `password.force`.
- [x] Tamu ke `dashboard` → redirect `login`; Dosen ke route `role:ADMIN`
      (route dummy di test) → 403.
- [x] Ganti password: password lama salah ditolak.
