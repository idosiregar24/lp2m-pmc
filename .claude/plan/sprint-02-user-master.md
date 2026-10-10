# Sprint 02 — Kelola User & Data Master

PRD §4.2, §4.3, §5.1–5.2, §7. Semua route di grup `role:ADMIN`.

## Data Master

- [ ] Migrasi `faculties`, `study_programs`, `schemes`, `funding_sources`
      (PRD §5.1) + FK `users.study_program_id` (`restrictOnDelete`).
- [ ] Enum `StudyProgramLevel` (D3/S1/S2/S3/Profesi), `SchemeType`
      (Research → "Penelitian", CommunityService → "Pengabdian").
- [ ] Model + `scopeActive()` + `options()` (`Scheme::options(SchemeType)`,
      `StudyProgram::options(?int $facultyId)` dikelompokkan per fakultas).
- [ ] Controller `Master\*` (index/create/store/edit/update/destroy) +
      Form Request; hapus ditolak bila sudah dipakai → pesan "Data master
      sudah dipakai, nonaktifkan saja."; toggle aktif.
- [ ] View `master/*` dengan `<x-ui.tabs>` Fakultas · Prodi · Skema ·
      Sumber Dana.
- [ ] `MasterDataSeeder` dengan contoh (daftar resmi menyusul — PRD §11.1 no. 5; Admin mengisi lewat UI).
- [ ] Navigasi: Data Master → `master.faculties.index`.

## Kelola User

- [ ] `UserController` index (cari nama/NIDN/username; filter fakultas,
      prodi, role, status; paginasi), create, store, show, edit, update,
      destroy, `toggleActive`, `resetPassword`.
- [ ] `Admin\StoreUserRequest` / `UpdateUserRequest`: aturan PRD §4.2
      (NIDN/NUPTK 10/16 digit wajib untuk Dosen, username regex, password
      min 8 saat buat / kosong = tidak diubah saat edit, prodi wajib untuk
      Dosen & harus milik fakultas terpilih).
- [ ] `UserService`: buat (password sementara → `must_change_password`),
      reset password (generate/isi manual → tampilkan sekali di flash),
      toggle aktif.
- [ ] `UserPolicy`: tidak bisa menonaktifkan / menurunkan role diri sendiri;
      hapus hanya akun tanpa data — buat method `User::hasActivityData()`
      sekarang (return `false`), lalu isi pemeriksaannya di Sprint 03–06
      setiap tabel data bertambah.
- [ ] Form: pilih fakultas → prodi tersaring (Alpine, opsi dari props).
- [ ] Detail user: data diri + ringkasan jumlah per modul (angka diisi
      bertahap per sprint modul).
- [ ] Navigasi: Pengguna → `users.index`.

## Pengaturan Sistem — logo (PRD §4.12)

- [ ] Migrasi `settings` (key UNIQUE, value), model `Setting`.
- [ ] `App\Support\Settings` (`get`, `logoUrl()`, cache `forever` +
      `forget` saat disimpan).
- [ ] `SettingService::updateLogo()`: simpan ke disk `public`
      (`branding/logo-{ulid}.{ext}`), hapus logo lama; `removeLogo()`.
- [ ] `Admin\SettingController` edit/update + `UpdateSettingRequest`
      (PNG/JPG/WebP, maks 1 MB, min 128×128, **tanpa SVG**).
- [ ] View `settings/edit`: pratinjau logo saat ini + unggah + tombol
      "Hapus logo".
- [ ] `php artisan storage:link` (hanya untuk logo) — catat di README.
- [ ] `<x-ui.logo>` membaca `Settings::logoUrl()`; sidebar, login, favicon.
- [ ] Navigasi: Pengaturan → `settings.edit`.

## Test

- [ ] Admin unggah logo PNG → tersimpan & tampil; SVG/>1 MB ditolak; logo
      lama terhapus saat diganti; Dosen 403.
- [ ] Admin CRUD user & master (200/302); Dosen 403; tamu → login.
- [ ] Validasi NIDN/NUPTK, username unik/regex, prodi-fakultas cocok.
- [ ] Admin tidak bisa menonaktifkan dirinya sendiri.
- [ ] Master terpakai tidak bisa dihapus; nonaktif tidak muncul di `options()`.
