# Sprint 01 — Autentikasi & Profil

PRD §2, §4.1, §9. Aturan: `rules/security-standards.md` §1.

## Database & model

- [ ] Migrasi `users` sesuai PRD §5.2 (tanpa `email`): `name`,
      `nidn_nuptk` NULL UNIQUE, `study_program_id` (`unsignedBigInteger`
      NULL tanpa FK — constraint ditambahkan migrasi Sprint 02 setelah
      tabel prodi ada), `username` UNIQUE, `password`, `role`, `is_active`,
      `must_change_password`, `last_login_at`, `remember_token`.
- [ ] Enum `UserRole` (Admin/Dosen, `label()`, `tone()`).
- [ ] Model `User`: `$fillable`, `casts()` (`role`, `is_active`,
      `must_change_password`, `last_login_at`, `password` → `hashed`),
      helper `isAdmin()`, `isDosen()`.
- [ ] `UserFactory` (+ state `admin()`, `inactive()`, `mustChangePassword()`).
- [ ] `UserSeeder`: `admin`/`password` (ADMIN), `dosen`/`password` (DOSEN).

## Login & sesi

- [ ] `LoginController` (create/store/destroy) + `Auth\LoginRequest`
      (username, password, remember).
- [ ] `RateLimiter::for('login')` 5/menit per `username|ip`; pesan generik.
- [ ] Tolak akun nonaktif dengan pesan PRD §4.1; perbarui `last_login_at`.
- [ ] Halaman `auth/login.blade.php` (`<x-layouts.guest>`): Username,
      Password, "Ingat saya", tombol Masuk.
- [ ] Middleware `EnsureRole` (`role`), `EnsureUserIsActive` (`active`),
      `EnsurePasswordChanged` (`password.changed`) — alias di
      `bootstrap/app.php`; redirect tamu ke `login`.
- [ ] `ForcePasswordChangeController` + halaman `auth/force-password`
      (password baru + konfirmasi).
- [ ] Logout (POST, invalidate session).

## Profil Saya

- [ ] `ProfileController@show`: data diri hanya-baca (nama, NIDN/NUPTK,
      fakultas, prodi, username, role).
- [ ] Ganti password: `current_password` + password baru + konfirmasi
      (`Profile\UpdatePasswordRequest`).
- [ ] Menu pengguna di topbar → Profil Saya, Keluar.

## Test

- [ ] Login sukses (username), gagal (pesan generik), throttle setelah 5x.
- [ ] Akun nonaktif ditolak login & sesi aktif diputus.
- [ ] `must_change_password` memaksa ke `password.force`.
- [ ] Tamu ke `dashboard` → redirect `login`; Dosen ke route `role:ADMIN`
      (route dummy di test) → 403.
- [ ] Ganti password: password lama salah ditolak.
