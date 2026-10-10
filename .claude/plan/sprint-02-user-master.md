# Sprint 02 — Kelola User & Data Master

PRD §4.2, §4.3, §5.1–5.2, §7. Semua route di grup `role:ADMIN`.

## Data Master

- [x] Migrasi `faculties`, `study_programs`, `schemes`, `funding_sources`
      (PRD §5.1) + FK `users.study_program_id` (`restrictOnDelete`).
- [x] Enum `StudyProgramLevel` (D3/S1/S2/S3/Profesi), `SchemeType`
      (Research → "Penelitian", CommunityService → "Pengabdian").
- [x] Model + `scopeActive()` + `options()` (`Scheme::options(SchemeType)`,
      `StudyProgram::options(?int $facultyId)` dikelompokkan per fakultas).
- [x] Controller `Master\*` (index/create/store/edit/update/destroy) +
      Form Request; hapus ditolak bila sudah dipakai → pesan "Data master
      sudah dipakai, nonaktifkan saja."; toggle aktif.
- [x] View `master/*` dengan `<x-ui.tabs>` Fakultas · Prodi · Skema ·
      Sumber Dana.
- [x] `MasterDataSeeder` dengan contoh (daftar resmi menyusul — PRD §11.1 no. 5; Admin mengisi lewat UI).
- [x] Navigasi: Data Master → `master.faculties.index`.

## Kelola User

- [x] `UserController` index (cari nama/NIDN/username; filter fakultas,
      prodi, role, status; paginasi), create, store, show, edit, update,
      destroy, `toggleActive`, `resetPassword`.
- [x] `Admin\StoreUserRequest` / `UpdateUserRequest`: aturan PRD §4.2
      (NIDN/NUPTK 10/16 digit wajib untuk Dosen, username regex, password
      min 8 saat buat / kosong = tidak diubah saat edit, prodi wajib untuk
      Dosen & harus milik fakultas terpilih).
- [x] `UserService`: buat (password sementara → `must_change_password`),
      reset password (generate/isi manual → tampilkan sekali di flash),
      toggle aktif.
- [x] `UserPolicy`: tidak bisa menonaktifkan / menurunkan role diri sendiri;
      hapus hanya akun tanpa data — buat method `User::hasActivityData()`
      sekarang (return `false`), lalu isi pemeriksaannya di Sprint 03–06
      setiap tabel data bertambah.
- [x] Form: pilih fakultas → prodi tersaring (Alpine, opsi dari props).
- [x] Detail user: data diri + ringkasan jumlah per modul (angka diisi
      bertahap per sprint modul).
- [x] Navigasi: Pengguna → `users.index`.

## Pengaturan Sistem — logo (PRD §4.12)

- [x] Migrasi `settings` (key UNIQUE, value), model `Setting`.
- [x] `App\Support\Settings` (`get`, `logoUrl()`, cache `forever` +
      `forget` saat disimpan).
- [x] `SettingService::updateLogo()`: simpan ke disk `public`
      (`branding/logo-{ulid}.{ext}`), hapus logo lama; `removeLogo()`.
- [x] `Admin\SettingController` edit/update + `UpdateSettingRequest`
      (PNG/JPG/WebP, maks 1 MB, min 128×128, **tanpa SVG**).
- [x] View `settings/edit`: pratinjau logo saat ini + unggah + tombol
      "Hapus logo".
- [x] `php artisan storage:link` (hanya untuk logo) — catat di README.
- [x] `<x-ui.logo>` membaca `Settings::logoUrl()`; sidebar, login, favicon.
- [x] Navigasi: Pengaturan → `settings.edit`.

## Test

- [x] Admin unggah logo PNG → tersimpan & tampil; SVG/>1 MB ditolak; logo
      lama terhapus saat diganti; Dosen 403.
- [x] Admin CRUD user & master (200/302); Dosen 403; tamu → login.
- [x] Validasi NIDN/NUPTK, username unik/regex, prodi-fakultas cocok.
- [x] Admin tidak bisa menonaktifkan dirinya sendiri.
- [x] Master terpakai tidak bisa dihapus; nonaktif tidak muncul di `options()`.
