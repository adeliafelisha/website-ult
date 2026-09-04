# Deployment prototype ULT ke Vercel

Repository sudah dikonfigurasi untuk Vercel Functions melalui `api/index.php` dan `vercel-php`. Vercel cocok untuk demo internal, tetapi database dan file upload harus memakai layanan persisten di luar filesystem Vercel.

## 1. Layanan yang dibutuhkan

- Vercel Hobby untuk menjalankan aplikasi.
- PostgreSQL terkelola, misalnya Neon atau Supabase, untuk seluruh data dan akun admin.
- Object storage kompatibel S3, misalnya Cloudflare R2, untuk upload gambar dari Filament.

SQLite dan folder `storage/app/public` lokal jangan digunakan di Vercel karena isi filesystem function tidak persisten.

## 2. Pengaturan project Vercel

Import repository Git ke Vercel. Pastikan **Root Directory** menunjuk ke folder yang berisi `artisan`, `composer.json`, dan `vercel.json`. Framework Preset boleh dibiarkan `Other`; `vercel.json` sudah mengatur install, build, output `public`, runtime PHP, dan routing.

Hapus override dashboard lama yang mengisi Output Directory dengan `dist`. Jika Vercel menampilkan nilai dari repository, gunakan `public`.

## 3. Environment Variables

Tambahkan ke Production dan Preview sesuai kebutuhan:

```dotenv
APP_NAME="ULT Unpad"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:HASIL_PHP_ARTISAN_KEY_GENERATE_SHOW
APP_URL=https://NAMA-PROJECT.vercel.app

LOG_CHANNEL=stderr
LOG_LEVEL=warning
CACHE_DRIVER=array
SESSION_DRIVER=cookie
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

DB_CONNECTION=pgsql
DATABASE_URL=postgresql://USER:PASSWORD@HOST/DATABASE?sslmode=require
DB_SSLMODE=require

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=auto
AWS_BUCKET=...
AWS_ENDPOINT=https://ENDPOINT-OBJECT-STORAGE
AWS_URL=https://DOMAIN-PUBLIK-BUCKET
AWS_USE_PATH_STYLE_ENDPOINT=false

ADMIN_EMAIL=alamat-admin
ADMIN_PASSWORD=password-unik-minimal-14-karakter
ADMIN_ALLOWED_DOMAINS=domain-email-admin
```

Buat `APP_KEY` di komputer lokal dengan `php artisan key:generate --show`. Jangan memasukkan `.env` atau rahasia ke Git.

Untuk email pribadi saat demo, isi `ADMIN_ALLOWED_DOMAINS` dengan domain setelah tanda `@`, misalnya `gmail.com`. Daftar dapat dipisahkan dengan koma.

## 4. Menyiapkan database

Jalankan migrasi dan seeder sekali terhadap database prototype sebelum dipakai tim:

```powershell
php artisan migrate --force
php artisan db:seed --force
```

Perintah tersebut harus dijalankan dengan environment production/database prototype yang benar. Seeder bersifat idempotent untuk konten awal dan membuat atau memperbarui akun berdasarkan `ADMIN_EMAIL` dan `ADMIN_PASSWORD`.

Jangan menjalankan `migrate:fresh` pada database yang sudah berisi konten karena perintah itu menghapus seluruh tabel.

## 5. Verifikasi sesudah deploy

1. Buka `/`, `/profil`, `/layanan`, satu detail layanan, `/artikel`, `/faq`, dan `/kontak`.
2. Uji pergantian ID/EN, mode malam, dan menu aksesibilitas.
3. Login ke `/admin`.
4. Buat satu draft, edit, publish, lalu pastikan perubahan tampil pada halaman publik.
5. Unggah satu gambar dan pastikan URL berasal dari object storage.
6. Cek Vercel Logs untuk error 500, database timeout, dan permission storage.

## 6. Keterbatasan prototype

- PHP berjalan melalui community runtime `vercel-php`, bukan runtime PHP bawaan Vercel.
- Function dapat mengalami cold start setelah lama tidak diakses.
- `CACHE_DRIVER=array` tidak menyimpan cache lintas request.
- Session disimpan dalam cookie agar login tidak bergantung pada disk sementara.
- Pekerjaan queue jangka panjang dan scheduler tidak dijalankan sebagai proses daemon.
- Letakkan database dan function di region yang dekat agar dashboard tidak lambat.

Untuk domain produksi `ult.unpad.ac.id`, server/container PHP konvensional atau platform yang mendukung Laravel secara native tetap lebih tepat untuk operasi jangka panjang.
