# bp2m-pmc

Sistem Informasi **BP2M Universitas PMC** — pendataan penelitian, pengabdian
kepada masyarakat, publikasi, buku, dan HKI dosen, lengkap dengan dokumen
pendukung dan verifikasi oleh admin BP2M.

Laravel 12 · PHP 8.4 · MySQL 8 · Blade + Tailwind CSS v4 + Alpine.js.
Spesifikasi & rencana kerja: [`.claude/`](.claude/CLAUDE.md).

## Menjalankan di lokal (Laragon)

```bash
composer install
npm install
cp .env.example .env          # isi DB_DATABASE=bp2m_pmc, DB_USERNAME=root
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link      # hanya untuk logo dari menu Pengaturan
npm run build                 # atau: npm run dev
php artisan serve --port=8020
```

Buka <http://localhost:8020>. Akun demo (password `password`):

| Username | Role |
|---|---|
| `admin` | Admin BP2M |
| `dosen` | Dosen |

## Tes

```bash
php artisan test          # Pest, SQLite in-memory
vendor/bin/pint --test
npm run build
```

Jangan menjalankan `php artisan config:cache` di lokal — konfigurasi yang
di-cache membuat tes memakai database MySQL dev.

## Production

Kebutuhan server: PHP 8.4 (ekstensi `gd`, `zip`, `xml`, `mbstring`,
`fileinfo` — dipakai ekspor Excel), MySQL 8, Nginx/Apache dengan document
root `public/`, HTTPS.

1. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`,
   `SESSION_SECURE_COOKIE=true`, kredensial DB, `ADMIN_INITIAL_PASSWORD`
   (min. 8 karakter).
2. Pasang & build:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan key:generate          # sekali saja, saat pertama
   php artisan migrate --force
   php artisan db:seed --class=ProductionSeeder   # sekali saja: master contoh + akun admin
   php artisan storage:link          # untuk logo dari menu Pengaturan
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
3. Izin tulis untuk user web server: `storage/` dan `bootstrap/cache/`.
4. Masuk sebagai `admin` (wajib ganti password), unggah logo di
   **Pengaturan**, isi daftar resmi fakultas/prodi/skema/sumber dana di
   **Data Master**, lalu buat akun dosen di **Pengguna**.
5. Cadangan rutin: database **dan** `storage/app/private` (dokumen
   pendukung, tidak bisa dibuat ulang).

Pembaruan: `git pull`, ulangi langkah 2 tanpa `key:generate` dan seeder.
