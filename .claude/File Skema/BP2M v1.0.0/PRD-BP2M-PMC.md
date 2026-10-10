# PRD — Sistem Informasi BP2M Universitas PMC

| | |
|---|---|
| Versi | 1.1.0 (draft) |
| Tanggal | 2026-10-10 |
| Sumber kebutuhan | Pesan Pak Anggy (Dosen Wali), 10/10/2026 13:10–13:13 |
| Status | Draft — §11.1 dijawab user 2026-10-10 (dosen input sendiri, verifikasi Admin, tanpa role Pimpinan, logo diunggah lewat sistem, master menyusul); butir **[ASUMSI]** tersisa di §11.2 |

---

## 1. Ringkasan

### 1.1 Latar belakang
BP2M (Badan Penelitian dan Pengabdian kepada Masyarakat **[ASUMSI: kepanjangan
resmi]**) Universitas PMC perlu satu tempat untuk mendata kinerja Tri Dharma
dosen di bidang riset: penelitian, pengabdian kepada masyarakat, publikasi,
buku, dan Hak Kekayaan Intelektual (HKI), lengkap dengan dokumen pendukungnya.
Data ini dipakai untuk rekap tahunan, laporan pimpinan, dan bahan akreditasi.

### 1.2 Tujuan
1. Semua data penelitian, pengabdian, publikasi, buku, dan HKI tersimpan
   terstruktur (bukan spreadsheet terpisah per orang).
2. Setiap data bisa ditelusuri ke dosen pelakunya (NIDN/NUPTK, fakultas, prodi).
3. Dokumen pendukung (SK, kontrak, laporan, artikel, sertifikat) tersimpan
   aman dan bisa diunduh oleh yang berhak.
4. Rekap per tahun / fakultas / prodi / dosen bisa dilihat dan diekspor.

### 1.3 Ruang lingkup v1.0
| Termasuk | Tidak termasuk (v1.0) |
|---|---|
| Kelola User (dosen & admin) | Pengajuan proposal + review reviewer |
| Data Master (fakultas, prodi, skema, sumber dana) | Role Pimpinan (Rektor/Dekan/Kaprodi) — belum dibutuhkan |
| Kelola Penelitian | Integrasi SINTA / BIMA / Garuda / Google Scholar |
| Kelola Pengabdian kepada Masyarakat | Pengelolaan anggaran/pencairan dana |
| Kelola Publikasi | Notifikasi email/WhatsApp |
| Kelola Buku | Halaman publik untuk tamu |
| Kelola HKI | |
| **Verifikasi data oleh Admin** (§4.11) | |
| Pengaturan Sistem: unggah logo (§4.12) | |
| Dashboard + rekap + ekspor Excel | |

### 1.4 Bahasa
Seluruh teks yang dilihat pengguna (menu, label, tombol, pesan validasi,
pesan sukses/gagal) dalam **Bahasa Indonesia**. Kode, nama tabel/kolom, dan
komentar dalam Bahasa Inggris.

---

## 2. Pengguna & Role

| Role | Kode | Siapa | Ringkasan akses |
|---|---|---|---|
| Admin | `ADMIN` | Staf BP2M | Semua data semua dosen; **memverifikasi** data dosen; kelola user, data master, pengaturan; rekap & ekspor |
| Dosen | `DOSEN` | Dosen tetap Universitas PMC | **Menginput sendiri** data miliknya dan mengajukannya untuk diverifikasi; hanya melihat data miliknya (§7) |

Tidak ada role lain di v1.x (role Pimpinan ditunda — keputusan 2026-10-10).

- Tidak ada registrasi mandiri — akun dibuat oleh Admin.
- Login memakai **username + password**.
- Akun tidak pernah dihapus bila sudah punya data; cukup **dinonaktifkan**
  (akun nonaktif tidak bisa login, datanya tetap tampil di rekap).

---

## 3. Stack

| Lapisan | Pilihan |
|---|---|
| Backend | Laravel 12, PHP 8.4 |
| Database | MySQL 8.0 (Laragon di lokal) |
| View | **Blade** (server-rendered; tanpa Inertia/React/Livewire) |
| CSS | Tailwind CSS v4 via Vite (`@tailwindcss/vite`) |
| JS | Alpine.js (interaksi kecil), Tom Select (pilih dosen yang bisa dicari) |
| Ikon | `blade-ui-kit/blade-heroicons` |
| Ekspor | `maatwebsite/excel` (Sprint 7) |
| Test | Pest, SQLite in-memory |

---

## 4. Modul & Fitur

### 4.1 Autentikasi & Profil
- Halaman login: field **Username** + **Password**, "Ingat saya".
- Gagal login → pesan generik "Username atau password salah." (tidak
  membocorkan mana yang salah). Dibatasi 5 percobaan/menit per
  username+IP.
- Akun nonaktif → "Akun Anda dinonaktifkan. Hubungi admin BP2M."
- Password yang di-set/di-reset Admin bersifat sementara: pengguna wajib
  menggantinya saat login berikutnya (`must_change_password`).
- **Profil Saya**: lihat data diri (nama, NIDN/NUPTK, fakultas, prodi,
  username — hanya-baca) dan ganti password (password lama wajib).

### 4.2 Kelola User (Admin)
Field:

| Field | Aturan |
|---|---|
| Nama | wajib, maks 150 karakter, termasuk gelar (mis. "Dr. Budi Santoso, M.Kom.") |
| NIDN/NUPTK | wajib untuk role Dosen, opsional untuk Admin; angka saja, **10 digit (NIDN/NIDK) atau 16 digit (NUPTK)**; unik |
| Fakultas | wajib untuk Dosen; pilih dari master |
| Prodi | wajib untuk Dosen; pilih dari master, **difilter oleh fakultas terpilih** |
| Username | wajib, unik, 4–30 karakter, huruf kecil/angka/titik/garis bawah, diawali huruf |
| Password | wajib saat buat (min 8 karakter); saat edit dikosongkan = tidak diubah |
| Role | wajib: Admin / Dosen |
| Status aktif | default aktif |

Fitur: daftar (cari nama/NIDN/username, filter fakultas/prodi/role/status,
paginasi), tambah, ubah, aktif/nonaktifkan, reset password, detail
(menampilkan ringkasan jumlah penelitian/pengabdian/publikasi/buku/HKI
dosen tersebut). Hapus hanya untuk akun yang belum punya data sama sekali.

Aturan:
- Admin tidak bisa menonaktifkan / menurunkan role dirinya sendiri (cegah
  sistem tanpa admin).
- Yang disimpan di `users` hanya `study_program_id`; fakultas diturunkan
  dari prodi (tidak ada dua kolom yang bisa saling bertentangan).

### 4.3 Data Master (Admin)
| Master | Field | Catatan |
|---|---|---|
| Fakultas | nama, kode (opsional) | |
| Prodi | fakultas, nama, jenjang (D3/S1/S2/S3/Profesi) | |
| Skema | jenis (Penelitian / Pengabdian), nama, aktif | mis. Penelitian Dasar, Penelitian Terapan, PKM, Pemberdayaan Kemitraan Masyarakat |
| Sumber Dana | nama, aktif | mis. Internal Universitas PMC, Hibah Kemdiktisaintek, Mandiri, Mitra/Industri |

- Master yang sudah dipakai data lain tidak bisa dihapus — hanya
  dinonaktifkan (tidak muncul lagi di pilihan form, data lama tetap tampil).
- Daftar resmi PMC **belum diterima** — seeder memakai data contoh; Admin
  mengisi/memperbaiki daftar resmi lewat menu Data Master begitu datanya
  ada (tidak perlu ubah kode).

### 4.4 Kelola Penelitian
| Field | Aturan |
|---|---|
| Judul penelitian | wajib, maks 500 karakter |
| Tahun | wajib, 4 digit, 1990 s.d. tahun berjalan + 1 |
| Peneliti | wajib ≥1 orang; urutan pertama = **Ketua**, sisanya Anggota (lihat §6.1) |
| Jenis skema | wajib, master Skema jenis Penelitian |
| Sumber dana | wajib, master Sumber Dana |
| Dokumen pendukung | opsional, bisa lebih dari satu (lihat §6.2) |

### 4.5 Kelola Pengabdian kepada Masyarakat
| Field | Aturan |
|---|---|
| Judul pengabdian | wajib, maks 500 karakter |
| Tahun | wajib (aturan sama) |
| Pengabdi | wajib ≥1; urutan pertama = **Ketua** |
| Jenis skema | wajib, master Skema jenis Pengabdian |
| Mitra | wajib, teks maks 255 (nama mitra/kelompok masyarakat/instansi) |
| Sumber dana | wajib, master Sumber Dana |
| Dokumen pendukung | opsional, bisa lebih dari satu |

### 4.6 Kelola Publikasi
| Field | Aturan |
|---|---|
| Judul publikasi | wajib, maks 500 |
| Penulis | wajib ≥1; urutan = urutan penulis di artikel (pertama = penulis pertama) |
| Tahun terbit | wajib |
| Jenis wadah **[ASUMSI]** | wajib: Jurnal / Prosiding — menentukan label "Nama jurnal"/"Nama prosiding" |
| Nama jurnal / prosiding | wajib, maks 255 |
| Level | wajib: Nasional / Internasional |
| Penerbit | wajib, maks 255 |
| Dokumen pendukung | opsional, bisa lebih dari satu |

### 4.7 Kelola Buku
| Field | Aturan |
|---|---|
| Judul buku | wajib, maks 500 |
| Tahun terbit | wajib |
| Penulis | wajib ≥1; urutan = urutan di sampul |
| Penerbit | wajib, maks 255 |
| Dokumen pendukung | opsional, bisa lebih dari satu |

> Buku ada di daftar field Pak Anggy tetapi tidak di daftar modul awal —
> diperlakukan sebagai modul tersendiri.

### 4.8 Kelola HKI
| Field | Aturan |
|---|---|
| Judul KI | wajib, maks 500 |
| Tahun terbit | wajib (tahun sertifikat/pencatatan) |
| Pemilik KI | wajib ≥1 (dosen internal dan/atau pihak luar, mis. "Universitas PMC") |
| Jenis KI | wajib: Hak Cipta, Paten, Paten Sederhana, Merek, Desain Industri, Desain Tata Letak Sirkuit Terpadu, Rahasia Dagang, Indikasi Geografis, Perlindungan Varietas Tanaman |
| Dokumen pendukung | opsional, bisa lebih dari satu |

### 4.9 Fitur bersama setiap modul data (4.4–4.8)
- **Daftar**: tabel berpaginasi (15/halaman), urut tahun terbaru; kolom
  judul, tahun, ketua/penulis pertama, atribut utama (skema/level/jenis),
  jumlah dokumen, **status verifikasi**.
- **Filter** (query string, di server): kata kunci (judul/nama anggota),
  tahun, fakultas & prodi (dari anggota internal), atribut modul (skema,
  sumber dana, level, jenis KI), **status**.
- **Tambah / Ubah**: satu form; anggota & dokumen diisi di form yang sama.
  Dosen punya dua tombol: **Simpan sebagai Draf** dan **Simpan & Ajukan**.
  Admin hanya **Simpan** (data buatan/ubahan Admin langsung Terverifikasi).
- **Detail**: semua field, status + riwayat verifikasi, catatan revisi
  terakhir (bila ada), daftar anggota (internal ber-link ke profil dosen,
  eksternal ditandai "Luar PMC"), daftar dokumen + unduh.
- **Hapus**: konfirmasi modal; soft delete. Dosen hanya bisa menghapus data
  berstatus Draf / Perlu Revisi.
- Dosen melihat menu yang sama, tetapi daftarnya hanya data miliknya (§7).

### 4.10 Dashboard & Rekap
- **Dashboard Admin**: kartu "Menunggu verifikasi" (jumlah Diajukan, link
  ke §4.11), kartu jumlah per modul (tahun berjalan + total, **hanya
  Terverifikasi**), grafik jumlah per tahun (5 tahun terakhir) per modul,
  rekap per fakultas.
- **Dashboard Dosen**: kartu jumlah data miliknya per modul (Terverifikasi),
  daftar **"Perlu revisi"** dan **"Draf belum diajukan"**, 5 data terbaru.
- **Rekap**: tabel per dosen (jumlah per modul, filter
  tahun/fakultas/prodi). Angka resmi = **Terverifikasi saja**.
- **Ekspor Excel** per modul sesuai filter yang sedang aktif (Admin; Dosen
  hanya datanya sendiri); default status Terverifikasi, kolom Status ikut.

### 4.11 Verifikasi Data (Admin)
Berlaku untuk kelima modul data (4.4–4.8). Status:

| Status | Kode | Arti | Dosen boleh ubah/hapus? |
|---|---|---|---|
| Draf | `DRAFT` | Disimpan dosen, belum diajukan | Ya |
| Diajukan | `SUBMITTED` | Menunggu diperiksa Admin | Tidak (terkunci) |
| Perlu Revisi | `NEEDS_REVISION` | Dikembalikan Admin dengan catatan | Ya, lalu ajukan ulang |
| Terverifikasi | `VERIFIED` | Sah; dihitung di dashboard/rekap/ekspor | Tidak (terkunci) |

- Halaman **Verifikasi** (menu Administrasi, angka antrean di menu): semua
  data berstatus Diajukan lintas modul (tab per modul + "Semua"), urut
  paling lama diajukan, filter fakultas/prodi/tahun.
- Di detail data Diajukan, Admin memilih **Verifikasi** atau **Kembalikan
  untuk Revisi** (catatan wajib, maks 1000 karakter).
- Admin bisa **Buka Kembali** data Terverifikasi → Perlu Revisi (catatan
  wajib) bila ternyata ada yang salah.
- Mengajukan butuh minimal **1 dokumen pendukung** **[ASUMSI]**
  (`config('bp2m.verification.require_document')`).
- Data yang dibuat atau diubah Admin langsung `VERIFIED` (Admin = verifikator).
- Setiap perpindahan status dicatat di `verification_logs` (siapa, kapan,
  dari → ke, catatan) — **append-only**, tampil sebagai riwayat di detail.
- Tidak ada notifikasi email/WA (di luar lingkup) — Dosen melihat status &
  catatan revisi di daftar, detail, dan dashboard.

### 4.12 Pengaturan Sistem (Admin)
- **Logo Universitas PMC** diunggah Admin dari menu Pengaturan (PNG/JPG/WebP,
  maks 1 MB, disarankan persegi ≥256px; SVG ditolak). Dipakai di sidebar,
  halaman login, dan favicon. Belum ada logo → lambang sementara (lingkaran
  emas bertulisan "PMC").
- Ganti logo menggantikan file lama (file lama dihapus).

---

## 5. Skema Database

Semua tabel `utf8mb4`, PK `id` BIGINT auto-increment, `timestamps()`.

### 5.1 Master
```
faculties        id, name(150) UNIQUE, code(20) NULL, is_active BOOL=1
study_programs   id, faculty_id FK→faculties RESTRICT, name(150), level(10) [D3|S1|S2|S3|PROFESI],
                 is_active BOOL=1, UNIQUE(faculty_id, name)
schemes          id, type(20) [RESEARCH|COMMUNITY_SERVICE], name(150), is_active BOOL=1,
                 UNIQUE(type, name)
funding_sources  id, name(150) UNIQUE, is_active BOOL=1
```

### 5.2 Pengguna
```
users  id, name(150), nidn_nuptk(16) NULL UNIQUE, study_program_id FK NULL RESTRICT,
       username(30) UNIQUE, password, role(20) [ADMIN|DOSEN], is_active BOOL=1,
       must_change_password BOOL=0, last_login_at NULL, remember_token
```
Tidak ada kolom `email` (login username). Jika kelak butuh reset password
lewat email, tambahkan `email NULL UNIQUE` di migrasi terpisah.

### 5.3 Data kinerja
Kelima tabel juga punya `created_by`/`updated_by` FK→users RESTRICT,
`softDeletes()`, dan kolom verifikasi:
```
status(20) [DRAFT|SUBMITTED|NEEDS_REVISION|VERIFIED] DEFAULT 'DRAFT',
submitted_at NULL, verified_at NULL, verified_by FK NULL→users RESTRICT,
revision_note TEXT NULL,  INDEX(status)
```
```
researches              id, title(500), year SMALLINT UNSIGNED, scheme_id FK RESTRICT,
                        funding_source_id FK RESTRICT
community_services      id, title(500), year, scheme_id FK, partner(255), funding_source_id FK
publications            id, title(500), year, venue_type(20) [JOURNAL|PROCEEDING],
                        venue_name(255), level(20) [NATIONAL|INTERNATIONAL], publisher(255)
books                   id, title(500), year, publisher(255)
intellectual_properties id, title(500), year, type(40) [COPYRIGHT|PATENT|SIMPLE_PATENT|TRADEMARK|
                        INDUSTRIAL_DESIGN|IC_LAYOUT_DESIGN|TRADE_SECRET|GEOGRAPHICAL_INDICATION|
                        PLANT_VARIETY]
```
Index: `year` di kelima tabel; `scheme_id`, `funding_source_id` di
researches & community_services; `level` di publications; `type` di
intellectual_properties.

> Catatan: model `Research` wajib `protected $table = 'researches';` —
> pluralizer Laravel menganggap "research" tak terhitung (default tabel
> `research`).

### 5.4 Polimorfik bersama
```
contributors   id, contributable_type(50), contributable_id, user_id FK NULL→users RESTRICT,
               name(150) NULL, affiliation(150) NULL, sort_order TINYINT UNSIGNED,
               INDEX(contributable_type, contributable_id), INDEX(user_id),
               UNIQUE(contributable_type, contributable_id, sort_order)
               -- user_id terisi = dosen internal (name/affiliation diabaikan);
               -- user_id NULL = pihak luar (name wajib)
documents      id, documentable_type(50), documentable_id, original_name(255), path(255),
               mime_type(100), size INT UNSIGNED, uploaded_by FK→users RESTRICT,
               INDEX(documentable_type, documentable_id)
```
Morph map (wajib, `Relation::enforceMorphMap`): `research`,
`community_service`, `publication`, `book`, `intellectual_property`.

### 5.4b Verifikasi & pengaturan
```
verification_logs  id, verifiable_type(50), verifiable_id, from_status(20) NULL, to_status(20),
                   note TEXT NULL, acted_by FK→users RESTRICT, created_at
                   INDEX(verifiable_type, verifiable_id)
                   -- append-only: tanpa updated_at, tanpa route ubah/hapus
settings           id, key(100) UNIQUE, value TEXT NULL, timestamps
                   -- key v1.1: 'logo_path'
```

### 5.5 Relasi
```
Faculty 1─* StudyProgram 1─* User
Scheme  1─* Research, CommunityService        FundingSource 1─* Research, CommunityService
{Research|CommunityService|Publication|Book|IntellectualProperty} 1─* Contributor *─1 User (nullable)
{…sama…} 1─* Document
{…sama…} 1─* VerificationLog *─1 User (acted_by)
```

---

## 6. Aturan bisnis bersama

### 6.1 Anggota (peneliti / pengabdi / penulis / pemilik KI)
- Satu tabel `contributors` untuk kelima modul; label di UI mengikuti modul
  (Peneliti, Pengabdi, Penulis, Pemilik KI).
- Setiap baris = **dosen internal** (pilih dari user aktif role Dosen,
  bisa dicari nama/NIDN) **atau pihak luar** (ketik nama + afiliasi
  opsional, mis. mahasiswa, dosen kampus lain, instansi).
- Urutan bisa diatur; `sort_order` 1 = Ketua (penelitian/pengabdian) /
  penulis pertama (publikasi/buku) / pemilik pertama (HKI).
- Minimal 1 anggota; dosen yang sama tidak boleh dua kali dalam satu data.
- Saat **Dosen** membuat data, dirinya otomatis ada di daftar anggota
  dan tidak bisa menghapus dirinya sendiri dari data itu.
- Fakultas/prodi sebuah data = fakultas/prodi anggota internalnya (dipakai
  filter & rekap; satu data bisa terhitung di lebih dari satu prodi).
- Disimpan lewat `ContributorService::sync()` — hapus-lalu-tulis-ulang dalam
  transaksi yang sama dengan data induknya.

### 6.2 Dokumen pendukung
- Format **PDF**, maks **10 MB** per file, maks **10 file** per data
  (`config/bp2m.php`).
- Disimpan di disk privat (`storage/app/private/documents/{morph}/{id}/`)
  dengan nama acak; nama asli disimpan di kolom `original_name`.
- Diunduh hanya lewat route `documents.download` yang memeriksa hak akses
  data induknya — tidak ada URL publik/`storage:link` untuk dokumen.
- Hapus dokumen satu per satu dari halaman Ubah (konfirmasi). File fisik
  dihapus saat dokumen dihapus; saat data induk di-soft-delete file tetap.

### 6.3 Tahun
`year` 4 digit, 1990 ≤ tahun ≤ tahun berjalan + 1 — satu aturan bersama
(`App\Rules\ValidYear`), dipakai semua modul.

### 6.4 Alur status verifikasi
```
           Simpan Draf           Ajukan (≥1 dokumen)          Verifikasi
 (baru) ──────────────▶ DRAFT ─────────────────────▶ SUBMITTED ───────────▶ VERIFIED
                          │                              │                    │
                          │                   Kembalikan │       Buka kembali │
                          │                (catatan wajib)        (catatan wajib)
                          │                              ▼                    │
                          │                       NEEDS_REVISION ◀────────────┘
                          │                              │
                          │                              └── Ajukan ulang ──▶ SUBMITTED
 Admin membuat/mengubah data ────────────────────────────────────────────▶ VERIFIED
```
- Perpindahan status hanya lewat `VerificationService` (`submit`, `verify`,
  `requestRevision`, `reopen`, `markVerifiedByAdmin`) — kolom `status` tidak
  pernah diisi dari form. Perpindahan di luar diagram ditolak.
- Dosen mengubah data `NEEDS_REVISION` → status tetap `NEEDS_REVISION`
  sampai ia menekan Ajukan.
- Menambah/menghapus dokumen mengikuti aturan ubah (terkunci untuk Dosen
  saat Diajukan/Terverifikasi).

---

## 7. Matriks Hak Akses (RBAC)

C = buat, R = lihat, U = ubah, D = hapus. **R\*** = hanya data miliknya.

| Fitur | ADMIN | DOSEN |
|---|---|---|
| Login, Profil Saya, ganti password | ✓ | ✓ |
| Dashboard | semua data | data sendiri |
| Kelola User | CRUD | — |
| Data Master | CRUD | — |
| Penelitian / Pengabdian / Publikasi / Buku / HKI | CRUD semua | C, R\*, U\*, D\* |
| Ajukan untuk verifikasi | — (data Admin langsung sah) | data miliknya (U\*) |
| Verifikasi / Kembalikan / Buka kembali | ✓ | — |
| Unduh dokumen | semua | dokumen data miliknya |
| Pengaturan Sistem (logo) | ✓ | — |
| Rekap & ekspor | semua | data sendiri |

**Definisi "milik":**
- **Lihat (R\*)**: dosen adalah pembuat data (`created_by`) **atau**
  tercatat sebagai anggota internal.
- **Ubah/Hapus/Ajukan (U\*, D\*)**: dosen adalah pembuat data **atau**
  anggota urutan pertama (ketua/penulis pertama/pemilik pertama)
  **[ASUMSI]**, **dan** status data `DRAFT` atau `NEEDS_REVISION`.

Dijaga di tiga lapis: middleware `role:ADMIN` untuk User, Master, Verifikasi, Pengaturan; Policy
per model data kinerja; scope query `visibleTo($user)` di halaman daftar.

---

## 8. UI / UX

- Layout: sidebar kiri (desktop) / menu geser (HP), topbar (judul halaman,
  menu pengguna: Profil Saya, Keluar), konten di kartu putih.
- Menu:
  - **Dashboard**
  - **Data Kinerja**: Penelitian · Pengabdian · Publikasi · Buku · HKI
  - **Administrasi** (Admin): Verifikasi (angka antrean) · Pengguna · Data Master (tab: Fakultas, Prodi, Skema, Sumber Dana) · Rekap · Pengaturan
- Gaya sederhana. Warna diambil dari poster resmi "Launching Universitas
  PMC": **merah `#B50606`** (primer — tombol utama, sidebar bergradasi ke
  `#421212`), **emas `#FFCE5F`** (aksen di atas merah, garis tepi),
  **biru `#1F4C82`** (sekunder/info), latar putih & abu hangat. Token lengkap
  di `.claude/rules/design-standards.md`, diterapkan di
  `resources/css/app.css`.
- Logo dari Pengaturan Sistem (§4.12), bukan file di repo.
- Status verifikasi tampil sebagai badge: Draf (abu), Diajukan (biru),
  Perlu Revisi (kuning), Terverifikasi (hijau).
- Kolom wajib ditandai bintang merah; tidak ada label "(opsional)".
- Pesan sukses/gagal muncul sebagai banner di atas konten.
- Responsif: bisa dipakai dari HP (form satu kolom, tabel bisa digeser).

---

## 9. Keamanan

- Session auth Laravel, CSRF di semua form, password bcrypt.
- Semua input lewat Form Request; `$fillable` eksplisit.
- Output Blade `{{ }}` (escaped) — `{!! !!}` dilarang untuk data pengguna.
- Upload: validasi ekstensi **dan** MIME PDF, nama file acak, disk privat.
- Semua halaman `X-Robots-Tag: noindex` (sistem internal).
- Data pribadi (NIDN/NUPTK) hanya terlihat oleh Admin dan pemiliknya.
- Jejak: `created_by`, `updated_by`, `timestamps`, soft delete di data
  kinerja; `verification_logs` append-only untuk setiap perubahan status.
- Logo satu-satunya file di disk `public`; dokumen pendukung selalu privat.

---

## 10. Definition of Done

Satu fitur dianggap selesai bila:
1. Sesuai field & aturan di §4–§6, RBAC sesuai §7.
2. Feature test Pest: alur sukses + validasi gagal + minimal 1 role yang
   ditolak (403) + dosen tidak bisa membuka data milik dosen lain.
3. `php artisan test` hijau, `npm run build` bersih, `vendor/bin/pint --test` bersih.
4. Tampil benar di lebar 375px dan 1280px.
5. Checklist sprint di `.claude/plan/` dicentang.

---

## 11. Keputusan

### 11.1 Sudah diputuskan (user, 2026-10-10)
1. **Dosen ikut menginput datanya sendiri** — ya.
2. **Verifikasi Admin** — ya (§4.11, §6.4).
3. **Role Pimpinan** — tidak untuk saat ini.
4. **Logo** — diunggah sendiri oleh BP2M lewat menu Pengaturan (§4.12).
   Warna dari poster resmi (§8).
5. **Daftar resmi fakultas, prodi, skema, sumber dana** — menyusul; sistem
   dibangun dengan data contoh, Admin mengisi lewat Data Master.

### 11.2 Masih terbuka
1. Kepanjangan resmi BP2M.
2. Siapa yang boleh mengubah data milik bersama: pembuat + ketua saja
   (asumsi sekarang) atau semua anggota internal?
3. Pengajuan wajib minimal 1 dokumen pendukung? (asumsi sekarang: ya)
4. Publikasi: perlu field tambahan (DOI/URL, volume/nomor, peringkat SINTA
   / Scopus Q1–Q4)? Buku: ISBN? HKI: nomor pencatatan/sertifikat?
5. Penelitian/Pengabdian: perlu jumlah dana (Rp)?
6. Format dokumen selain PDF (JPG/PNG untuk sertifikat)?
7. Server produksi (hosting kampus / VPS) dan domain.
