# Sprint 00 — Setup Proyek & Kerangka Tampilan

Tujuan: proyek Laravel 12 jalan di lokal dengan token warna PMC, layout
`app`/`guest`, dan komponen dasar — belum ada fitur bisnis.

## Instalasi

- [x] Pastikan PHP 8.4 aktif (`php -v`), MySQL Laragon jalan.
- [x] Git sudah di-init (branch `main`, remote `origin` →
      `github.com/idosiregar24/bp2m-pmc`), `README.md` + `.gitignore`
      awal sudah ada. User yang commit & push.
- [x] Root proyek sudah berisi `.claude/`, `.git/`, `README.md`,
      `.gitignore` → buat skeleton di folder sementara lalu pindahkan:
      `composer create-project laravel/laravel ../bp2m-tmp` → pindahkan
      semua isi (termasuk file tersembunyi) ke `bp2m-pmc/` **kecuali**
      `README.md` → **gabungkan** `.gitignore` Laravel dengan yang sudah
      ada (pertahankan `.claude/settings.local.json`, pastikan `.env`
      diabaikan) → hapus `../bp2m-tmp`.
- [x] `README.md` proyek: deskripsi singkat + cara menjalankan lokal.
- [x] `.env`: `APP_NAME="BP2M Universitas PMC"`, `APP_LOCALE=id`,
      `APP_FALLBACK_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`,
      `APP_TIMEZONE`→ `config/app.php` `'timezone' => 'Asia/Jakarta'`,
      `DB_CONNECTION=mysql`, `DB_DATABASE=bp2m_pmc`, `DB_USERNAME=root`,
      `DB_PASSWORD=`; `ADMIN_INITIAL_PASSWORD=` di `.env.example`. (sudah kecuali `ADMIN_INITIAL_PASSWORD`, menyusul Sprint 01)
- [x] Buat database `bp2m_pmc` (utf8mb4_unicode_ci). — Laravel 12.69.3 terpasang, migrasi bawaan sudah jalan di MySQL.
- [x] Composer: `blade-ui-kit/blade-heroicons`; dev: `pestphp/pest`,
      `pestphp/pest-plugin-laravel` (`./vendor/bin/pest --init`).
- [x] NPM: `alpinejs`, `@alpinejs/focus`, `tom-select`,
      `@fontsource-variable/inter` (Tailwind v4 + `@tailwindcss/vite` sudah
      bawaan Laravel 12 — cek `vite.config.js`).
- [x] Hapus yang tidak dipakai: `welcome.blade.php`, kolom
      `email`/`email_verified_at` & tabel `password_reset_tokens` di
      migrasi users (diganti di Sprint 01).
- [x] Bahasa: `lang/id/validation.php`, `auth.php`, `pagination.php`,
      `passwords.php` (Bahasa Indonesia).
- [x] `config/bp2m.php`: `per_page` 15, `min_year` 1990,
      `documents` (`max_kb` 10240, `max_files` 10, `mimes` `['pdf']`).
- [x] `phpunit.xml`: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`.

## Tampilan dasar

- [x] `resources/css/app.css`: token `@theme` persis
      `rules/design-standards.md` §1, `@source '../views'`.
- [x] `resources/js/app.js`: Alpine + `@alpinejs/focus`, `Alpine.start()`.
- [x] `components/layouts/app.blade.php`: sidebar gradasi merah dari
      `App\Support\Navigation` (semua item masih "Segera" kecuali
      Dashboard), topbar (breadcrumb, menu pengguna), area flash, slot konten.
- [x] `components/layouts/guest.blade.php`: latar gradasi poster + kartu putih.
- [x] `App\Support\Navigation`: grup Dashboard · Data Kinerja (Penelitian,
      Pengabdian, Publikasi, Buku, HKI) · Administrasi (Pengguna, Data
      Master, Rekap — `roles: ['ADMIN']`); item: label, ikon, route (null =
      Segera), pola aktif, roles.
- [x] Komponen `ui/`: button (primary/secondary/ghost/danger, `href` →
      `<a>`), badge, card, alert, empty-state, modal.
- [x] Komponen `form/`: label (`required`), input, select, textarea, error,
      field.
- [x] Komponen `shared/`: page-header.
- [x] `<x-ui.logo>` dengan placeholder (lingkaran emas "PMC"); sumber logo
      dari Pengaturan disambungkan di Sprint 02.
- [x] Halaman `dashboard.blade.php` placeholder (route `dashboard` sementara
      tanpa auth, diamankan di Sprint 01).
- [x] Middleware `X-Robots-Tag: noindex, nofollow` + `public/robots.txt`
      `Disallow: /`.

## Selesai bila

- [x] `php artisan serve --port=8020` menampilkan layout dengan warna PMC
      di 375px & 1280px.
- [x] `npm run build` bersih, `php artisan test` hijau (test contoh),
      `vendor/bin/pint --test` bersih.
- [x] Tidak ada hex/warna default Tailwind di `resources/views`.
