# Security Standards

Turunan PRD §7 (hak akses) dan §9 (keamanan). Aturan kepemilikan data dan
kerahasiaan dokumen adalah *business rule*, bukan sekadar hardening.

## 1. Authentication

- Session login (guard `web`) dengan **username + password**; tidak ada
  registrasi mandiri, login e-mail, atau reset password lewat e-mail. Lupa
  password → Admin me-reset.
- Rate limit `throttle:login` (`RateLimiter::for('login')` di
  `AppServiceProvider`): 5 percobaan/menit per `username|ip`. Pesan gagal
  generik "Username atau password salah." — tidak membocorkan apakah
  username ada.
- Akun nonaktif: login ditolak ("Akun Anda dinonaktifkan. Hubungi admin
  BP2M.") dan sesi aktifnya diputus oleh `EnsureUserIsActive` (alias
  `active`) di request berikutnya.
- Password: cast `hashed` (bcrypt default), minimal 8 karakter
  (`Password::min(8)`). Password yang di-set/di-reset Admin bersifat
  sementara → `must_change_password = true` → `EnsurePasswordChanged`
  (alias `password.changed`) mengarahkan ke `password.force` sampai diganti.
  Ganti password di Profil wajib password lama (`current_password`).
- `session()->regenerate()` setelah login; `invalidate()` +
  `regenerateToken()` saat logout. `last_login_at` diperbarui saat login.

## 2. Authorization — 3 lapis

1. **Route middleware** `role:ADMIN` (`EnsureRole`, membaca `users.role`)
   untuk Pengguna, Data Master, Verifikasi, Rekap, Pengaturan.
2. **Policy** per record:
   - `ActivityPolicy` (abstract; `ResearchPolicy`, `CommunityServicePolicy`,
     `PublicationPolicy`, `BookPolicy`, `IntellectualPropertyPolicy`
     mewarisinya):
     - `viewAny`, `create` → semua user aktif;
     - `view` → ADMIN, pembuat, atau anggota internal;
     - `update`, `delete`, `submit` → ADMIN (kecuali `submit`: Admin tidak
       mengajukan), atau pembuat / anggota urutan 1 **[ASUMSI PRD §7]**
       **dan** status `DRAFT`/`NEEDS_REVISION` (`!isLockedForOwner()`);
     - `verify`, `requestRevision`, `reopen` → ADMIN saja;
     - `restore`, `forceDelete` → ADMIN.
   - `DocumentPolicy`: `download` = boleh `view` induk; `delete` = boleh
     `update` induk.
   - `UserPolicy`: Admin tidak bisa menonaktifkan/menurunkan role dirinya
     sendiri; `delete` hanya untuk akun tanpa data (sebagai pembuat maupun
     anggota).
3. **Scope query** `visibleTo($user)` di setiap daftar, pencarian,
   dashboard, rekap, ekspor — data dosen lain tidak boleh muncul di mana
   pun, termasuk hitungan angka.

**Verifikasi:** `status` dan kolom verifikasi tidak pernah fillable / tidak
pernah ada di `rules()` — nilai `status` yang diselipkan ke request
diabaikan. Route verifikasi menerima `{type}` hanya dari whitelist alias
morph (`App\Support\Verifiable`, alias asing → 404), lalu tetap
`authorize()` lewat Policy model hasil resolve. `verification_logs` tidak
punya route ubah/hapus untuk siapa pun.

`ContributorService` menolak (ValidationException) bila Dosen yang
menyimpan menghapus dirinya sendiri dari daftar anggota, atau memasukkan
dosen yang sama dua kali.

## 3. Validasi input

- **Semua** input lewat Form Request; tidak ada `$request->all()` ke
  `create()`. `$fillable` eksplisit sebagai lapis kedua.
- Skema sesuai jenis modul & aktif:
  `Rule::exists('schemes', 'id')->where('type', SchemeType::Research->value)->where('is_active', true)`.
  Saat update, nilai lama yang kini nonaktif tetap diterima.
- Tahun: `App\Rules\ValidYear` (1990 ≤ tahun ≤ tahun berjalan + 1).
- Anggota: `contributors` array min 1; `contributors.*.user_id` →
  `exists:users,id` + role DOSEN aktif; `contributors.*.name` →
  `required_without:contributors.*.user_id|max:150`; `distinct` pada
  `user_id`.
- NIDN/NUPTK: `digits:10` atau `digits:16` (regex `^(\d{10}|\d{16})$`),
  unik, wajib bila role DOSEN.
- Username: `regex:/^[a-z][a-z0-9._]{3,29}$/`, unik.

## 4. Dokumen pendukung

- Disk **`local`** (`storage/app/private`) — tidak pernah `public`, tidak
  ada URL langsung, tidak ada `storage:link` untuk dokumen.
- Aturan: `file|mimes:pdf|mimetypes:application/pdf|max:{config('bp2m.documents.max_kb')}`,
  maksimal `config('bp2m.documents.max_files')` file per data (dihitung
  dengan yang sudah ada).
- Nama simpan acak `Str::ulid().'.pdf'` di `documents/{morph}/{id}/`;
  `original_name` = `basename()` nama asli, dipotong 255, hanya dipakai di
  header unduhan.
- `DocumentController@download`: `authorize('download', $document)` →
  `Storage::disk('local')->download($path, $originalName)` (attachment,
  bukan inline).
- `DocumentService::delete()` menghapus baris **dan** file fisik.

### Logo (satu-satunya file publik)

- Diunggah Admin di `settings.update`:
  `image|mimes:png,jpg,jpeg,webp|max:1024|dimensions:min_width=128,min_height=128`.
  **SVG ditolak** (bisa memuat script bila dibuka langsung).
- Disimpan di disk `public` (`branding/logo-{ulid}.{ext}`), path di
  `settings.logo_path`; logo lama dihapus saat diganti. `storage:link`
  hanya untuk ini — dokumen pendukung tetap di disk `local`.

## 5. Output & ekspor

- Blade `{{ }}` saja untuk data user.
- Ekspor Excel memakai scope `visibleTo` + filter yang sama dengan daftar;
  tidak menyertakan path file. Sel yang diawali `=`, `+`, `-`, `@` diberi
  awalan `'` (cegah formula injection).
- NIDN/NUPTK hanya tampil ke Admin dan pemiliknya — tidak di daftar anggota
  data milik dosen lain.

## 6. Data protection

- `.env` tidak pernah di-commit; secret baru → `.env.example` sebagai
  placeholder kosong (`ADMIN_INITIAL_PASSWORD=`).
- Production: `APP_DEBUG=false`, HTTPS, `SESSION_SECURE_COOKIE=true`.
- Sistem internal: middleware menambah `X-Robots-Tag: noindex, nofollow`;
  `public/robots.txt` → `Disallow: /`.

## 7. Sebelum merge

Jalankan `/security-review` untuk perubahan yang menyentuh login/password,
`EnsureRole`/Policy/`visibleTo`, unggah/unduh dokumen, atau ekspor. Setiap
route ber-role wajib feature test: role berhak (200/302), role tidak berhak
(403), tamu (redirect `login`).
