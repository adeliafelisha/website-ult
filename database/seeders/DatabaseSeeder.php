<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Faq;
use App\Models\QuickLink;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');
        if (app()->environment('production') && (! $adminEmail || ! $adminPassword || strlen($adminPassword) < 14)) {
            throw new \RuntimeException('ADMIN_EMAIL dan ADMIN_PASSWORD minimal 14 karakter wajib disetel untuk seeding production.');
        }
        if ($adminEmail && $adminPassword) {
            User::updateOrCreate(['email' => $adminEmail], ['name' => 'Admin ULT', 'password' => Hash::make($adminPassword), 'email_verified_at' => now(), 'is_admin' => true]);
        }
        $cats = [
            ['Mahasiswa, Calon Mahasiswa, dan Alumni', 'mahasiswa-calon-mahasiswa-alumni', 'Registrasi, penerimaan, UKT, dokumen akademik, kelulusan, dan layanan alumni.', 'Students, Prospective Students, and Alumni', 'Admissions, registration, tuition, academic documents, graduation, and alumni services.'],
            ['Dosen dan Tenaga Kependidikan', 'dosen-tenaga-kependidikan', 'Layanan administrasi dan dukungan bagi dosen serta tenaga kependidikan.', 'Lecturers and Education Staff', 'Administrative services and support for lecturers and education staff.'],
            ['Teknologi Informasi', 'teknologi-informasi', 'Akun PAuS, SIAT, jaringan, aplikasi, dan dukungan teknologi informasi.', 'Information Technology', 'PAuS accounts, SIAT, networks, applications, and information technology support.'],
            ['Internasional', 'internasional', 'Mobilitas internasional, exchange, visa, KITAS, dan study permit.', 'International', 'International mobility, exchange, visa, KITAS, and study permit services.'],
            ['Sarana dan Prasarana', 'sarana-prasarana', 'Informasi fasilitas kampus, aksesibilitas, ruang, dan dukungan sarana prasarana.', 'Facilities and Infrastructure', 'Campus facilities, accessibility, rooms, and infrastructure support.'],
        ];
        foreach ($cats as $i => $c) {
            ServiceCategory::updateOrCreate(['slug' => $c[1]], ['name' => $c[0], 'description' => $c[2], 'name_en' => $c[3], 'description_en' => $c[4], 'sort_order' => $i, 'is_featured' => true]);
        }
        $services = [
            ['akademik-mahasiswa', 'Penggantian KTM Hilang atau Rusak', 'penggantian-ktm', 'Panduan persyaratan dan langkah penggantian Kartu Tanda Mahasiswa.', 'Mahasiswa Aktif', 'Surat kehilangan dari kepolisian untuk KTM hilang.\nIdentitas mahasiswa aktif.', 'Surat kehilangan, KTP, dan pas foto terbaru.', '<ol><li>Siapkan seluruh dokumen.</li><li>Hubungi ULT untuk verifikasi.</li><li>Ikuti arahan pada kanal resmi.</li></ol>', 'hybrid', 'Hubungi Admin ULT', null],
            ['akademik-mahasiswa', 'Informasi UKT dan Registrasi', 'informasi-ukt-registrasi', 'Informasi pembayaran UKT, heregistrasi, dan kanal resmi bantuan.', 'Mahasiswa Aktif', 'Status mahasiswa aktif dan data pembayaran.', 'Bukti pembayaran jika diperlukan.', '<ol><li>Periksa tagihan pada sistem akademik.</li><li>Lakukan pembayaran melalui kanal resmi.</li><li>Hubungi ULT bila status belum diperbarui.</li></ol>', 'online', 'Buka PAuS', 'https://paus.unpad.ac.id'],
            ['mahasiswa-baru-smup', 'Registrasi Mahasiswa Baru', 'registrasi-mahasiswa-baru', 'Panduan registrasi, verifikasi dokumen, dan akses informasi penerimaan.', 'Mahasiswa Baru', 'Dinyatakan diterima melalui jalur resmi.', 'Dokumen sesuai ketentuan portal SMUP.', '<ol><li>Buka portal SMUP.</li><li>Masuk dengan akun peserta.</li><li>Ikuti tahapan dan tenggat.</li></ol>', 'online', 'Buka Website SMUP', 'https://smup.unpad.ac.id'],
            ['layanan-disabilitas', 'Pendampingan Layanan Disabilitas', 'pendampingan-layanan-disabilitas', 'Informasi akses fasilitas dan pendampingan layanan kampus.', 'Pengguna dengan Disabilitas', 'Sampaikan kebutuhan akses yang diperlukan.', 'Dokumen pendukung bila relevan.', '<ol><li>Pelajari opsi akses.</li><li>Hubungi ULT sebelum kunjungan.</li><li>Sepakati pendampingan yang dibutuhkan.</li></ol>', 'hybrid', 'Hubungi Admin ULT', null],
            ['paus-ti', 'Bantuan Akun PAuS', 'bantuan-akun-paus', 'Arah layanan kendala akun, lupa kata sandi, dan akses sistem.', 'Mahasiswa, Dosen & Tendik', 'Memiliki identitas sivitas Unpad.', 'NPM/NIP dan identitas pendukung.', '<ol><li>Buka PAuS.</li><li>Gunakan fitur pemulihan akun.</li><li>Gunakan Support Unpad jika perlu.</li></ol>', 'online', 'Buka PAuS', 'https://paus.unpad.ac.id'],
            ['layanan-internasional', 'Informasi Mahasiswa Internasional', 'informasi-mahasiswa-internasional', 'Routing informasi exchange, visa, KITAS, dan study permit.', 'Pengguna Internasional', 'Sesuai program dan status studi.', 'Paspor dan dokumen program.', '<ol><li>Periksa informasi program.</li><li>Siapkan dokumen perjalanan.</li><li>Hubungi International Office.</li></ol>', 'online', 'Buka International Office', 'https://international.unpad.ac.id']];
        $categoryAliases = ['akademik-mahasiswa' => 'mahasiswa-calon-mahasiswa-alumni', 'mahasiswa-baru-smup' => 'mahasiswa-calon-mahasiswa-alumni', 'dosen-tendik' => 'dosen-tenaga-kependidikan', 'paus-ti' => 'teknologi-informasi', 'layanan-internasional' => 'internasional', 'layanan-disabilitas' => 'sarana-prasarana'];
        foreach ($services as $s) {
            $cat = ServiceCategory::where('slug', $categoryAliases[$s[0]] ?? $s[0])->firstOrFail();
            Service::updateOrCreate(['slug' => $s[2]], ['service_category_id' => $cat->id, 'title' => $s[1], 'summary' => $s[3], 'audience' => $s[4], 'requirements' => $s[5], 'documents' => $s[6], 'procedure' => $s[7], 'delivery_type' => $s[8], 'cta_label' => $s[9], 'cta_url' => $s[10], 'content_owner' => 'ULT Unpad', 'responsible_unit' => 'Unit terkait — perlu verifikasi', 'is_featured' => true, 'is_published' => true, 'published_at' => now(), 'keywords' => explode(' ', strtolower($s[1]))]);
        }
        foreach ([['Panduan Cepat Menemukan Layanan Kampus', 'panduan-menemukan-layanan', 'Panduan', 'Mulai dari kebutuhanmu, lalu ikuti satu tindakan utama pada setiap layanan.', '<p>Gunakan pencarian, pilih layanan, baca persyaratan, lalu ikuti tombol tindakan menuju kanal resmi.</p>'], ['Kampus Inklusif: Mengenal Fasilitas Aksesibilitas', 'kampus-inklusif-fasilitas-aksesibilitas', 'Aksesibilitas', 'Kenali dukungan akses dan cara meminta pendampingan sebelum berkunjung.', '<p>Hubungi kanal resmi sebelum kunjungan agar kebutuhan akses dapat disiapkan.</p>'], ['Persiapan Administrasi Mahasiswa Baru', 'persiapan-administrasi-mahasiswa-baru', 'Mahasiswa Baru', 'Daftar ringkas untuk menyiapkan proses registrasi.', '<p>Pastikan data identitas konsisten dan proses dilakukan melalui portal resmi SMUP.</p>']] as $i => $a) {
            Article::updateOrCreate(['slug' => $a[1]], ['title' => $a[0], 'category' => $a[2], 'excerpt' => $a[3], 'content' => $a[4], 'author' => 'Tim ULT Unpad', 'content_owner' => 'ULT Unpad', 'is_featured' => true, 'is_published' => true, 'published_at' => now()->subDays($i * 4), 'keywords' => explode(' ', strtolower($a[0]))]);
        }
        foreach ([['Bagaimana cara menemukan layanan yang tepat?', 'Gunakan pencarian global atau pilih kategori berdasarkan kebutuhan. Setiap layanan memiliki langkah berikutnya yang jelas.', 'Umum'], ['Apakah website ULT membuat tiket pengaduan?', 'Tidak. Website ULT adalah portal informasi yang mengarahkan Anda ke sistem atau kanal resmi.', 'Umum'], ['Apa yang dilakukan jika tautan eksternal bermasalah?', 'Buka halaman Kontak untuk kanal alternatif resmi, lalu sampaikan nama layanan dan kendalanya.', 'Teknis'], ['Bagaimana meminta pendampingan aksesibilitas?', 'Buka Layanan Disabilitas lalu hubungi ULT sebelum kunjungan.', 'Aksesibilitas']] as $i => $f) {
            Faq::updateOrCreate(['question' => $f[0]], ['answer' => $f[1], 'category' => $f[2], 'sort_order' => $i, 'is_featured' => true, 'is_published' => true]);
        }
        foreach ([['Website Unpad', 'https://www.unpad.ac.id', 'Informasi utama Universitas Padjadjaran'], ['SMUP', 'https://smup.unpad.ac.id', 'Seleksi masuk dan registrasi'], ['PAuS', 'https://paus.unpad.ac.id', 'Akses akun dan sistem terintegrasi'], ['PPID Unpad', 'https://ppid.unpad.ac.id', 'Informasi publik']] as $i => $l) {
            QuickLink::updateOrCreate(['name' => $l[0]], ['url' => $l[1], 'description' => $l[2], 'sort_order' => $i, 'is_published' => true]);
        }
        $this->call(ContactSeeder::class);
    }
}
