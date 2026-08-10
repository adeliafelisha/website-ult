# Website ULT Universitas Padjadjaran

Portal informasi dan pengarah layanan Unit Layanan Terpadu (ULT) Unpad. Aplikasi ini mengikuti Product Blueprint v2.0

## Fitur

- Homepage responsif yang berorientasi mahasiswa, direktori dan detail layanan.
- Pencarian global lintas layanan, artikel, dan FAQ; zero-result dicatat untuk evaluasi.
- Artikel/informasi, FAQ, kontak kontekstual, dan quick links sistem eksternal.
- CMS Filament di `/admin` untuk kategori, layanan, artikel, FAQ, kontak, serta tautan.
- Workflow draft/publish, waktu terbit, content owner, metadata SEO, keyword, dan gambar.
- Aksesibilitas dasar: semantic HTML, skip link, keyboard focus, high contrast, ukuran teks, reduced motion, dan layout responsif.
- Analytics internal yang hanya menyimpan query pencarian, jumlah hasil, klik keluar, dan hash IP.

## Stack

- PHP 8.1+, Laravel 10, Filament 3.2, Livewire 3
- MySQL/MariaDB (produksi) atau SQLite (lokal/tes)
- Vite, vanilla JavaScript, CSS custom, Poppins

## Instalasi lokal

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env`, kemudian:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Buka aplikasi di `http://127.0.0.1:8000` dan CMS di `http://127.0.0.1:8000/admin`.

Seeder lokal membuat akun awal berdasarkan `ADMIN_EMAIL` dan `ADMIN_PASSWORD`. Nilai fallback hanya untuk development: `admin@ult.unpad.ac.id` / `admin12345`. Ubah kredensial sebelum deploy.

## Variabel penting

```dotenv
APP_NAME="ULT Unpad"
APP_URL=http://127.0.0.1:8000
ADMIN_EMAIL=admin@ult.unpad.ac.id
ADMIN_PASSWORD=ganti-dengan-password-kuat
```

Untuk SQLite, buat file `database/database.sqlite`, lalu gunakan `DB_CONNECTION=sqlite` dan kosongkan variabel MySQL. Untuk produksi gunakan MySQL, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, serta worker/backup terjadwal.

## Model konten

- `service_categories` → mengelompokkan kebutuhan pengguna.
- `services` → ringkasan, sasaran, syarat, dokumen, prosedur, tipe layanan, CTA, lokasi, jadwal, estimasi, biaya, unit, owner, SEO, dan publikasi.
- `articles` → artikel terstruktur yang dikelola admin.
- `faqs`, `quick_links`, `contacts` → self-service dan external routing.
- `search_events`, `outbound_clicks` → metrik discovery yang minim data pribadi.

## Operasional konten

1. Admin membuat konten sebagai draft dan melengkapi pemilik konten.
2. Reviewer memverifikasi syarat, prosedur, kontak, jam, serta URL eksternal.
3. Admin mengaktifkan `Terbit` dan mengatur waktu publikasi.
4. Kontak dan jam ditinjau tiap 3 bulan; link kritikal tiap bulan; prosedur tiap 6 bulan atau saat kebijakan berubah.

Data contoh yang menyebut unit, kontak, jadwal, dan fasilitas harus diverifikasi stakeholder sebelum produksi. Gambar contoh dapat diganti melalui file publik atau upload CMS.

## Pengujian

```bash
php artisan test
npm run build
php artisan route:list
```

## Deployment ringkas

Jalankan `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, `php artisan migrate --force`, `php artisan storage:link`, lalu cache konfigurasi/rute/view. Pastikan direktori `storage` dan `bootstrap/cache` writable serta backup database dan uploads aktif.
