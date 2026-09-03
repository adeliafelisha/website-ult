# Keamanan ULT Laravel

## Pelaporan kerentanan

Jangan membuat issue publik yang berisi kredensial atau detail eksploitasi. Laporkan secara privat kepada pengelola TIK/ULT Universitas Padjadjaran dan sertakan URL, waktu kejadian, serta langkah reproduksi tanpa menyertakan data pribadi yang tidak diperlukan.

## Persyaratan production

- Gunakan HTTPS saja dan arahkan HTTP ke HTTPS di Nginx/Apache.
- Set `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL=https://ult.unpad.ac.id`.
- Buat `APP_KEY` unik dengan `php artisan key:generate`; jangan pernah mengganti key tanpa rencana rotasi karena data terenkripsi dan session lama tidak lagi dapat dibaca.
- Gunakan akun database khusus aplikasi dengan izin hanya pada database ULT. Jangan gunakan akun `root`.
- Set `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `SESSION_EXPIRE_ON_CLOSE=true`, dan masa session singkat.
- Isi `ADMIN_ALLOWED_DOMAINS=ult.unpad.ac.id,unpad.ac.id`; setiap akun admin harus memiliki `is_admin=1` dan `email_verified_at`.
- Jangan menyimpan `.env`, dump database, private key, atau backup di dalam folder `public` maupun repository.
- Backup database dan `storage/app/public` secara terenkripsi, uji proses restore, dan batasi akses backup.
- Jalankan `composer audit --locked` dan `npm audit --omit=dev` dalam CI/deployment.
- Jalankan `php artisan migrate --force`, `php artisan optimize`, dan `php artisan icons:cache` saat deployment. Versi Filament yang dipakai proyek ini belum menyediakan perintah `filament:optimize`.
- Aktifkan log rotation, pemantauan login gagal, alert error 5xx, serta backup otomatis.

## Contoh izin database

Berikan `SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP` hanya pada database aplikasi untuk proses deployment. Untuk runtime yang lebih ketat, gunakan user migrasi terpisah dan user aplikasi tanpa izin DDL.
