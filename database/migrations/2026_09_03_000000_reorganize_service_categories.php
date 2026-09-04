<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categories = [
            'mahasiswa-calon-mahasiswa-alumni' => ['Mahasiswa, Calon Mahasiswa, dan Alumni', 'Students, Prospective Students, and Alumni', 'Registrasi, penerimaan, UKT, dokumen akademik, kelulusan, dan layanan alumni.', 'Admissions, registration, tuition, academic documents, graduation, and alumni services.'],
            'dosen-tenaga-kependidikan' => ['Dosen dan Tenaga Kependidikan', 'Lecturers and Education Staff', 'Layanan administrasi dan dukungan bagi dosen serta tenaga kependidikan.', 'Administrative services and support for lecturers and education staff.'],
            'teknologi-informasi' => ['Teknologi Informasi', 'Information Technology', 'Akun PAuS, SIAT, jaringan, aplikasi, dan dukungan teknologi informasi.', 'PAuS accounts, SIAT, networks, applications, and information technology support.'],
            'internasional' => ['Internasional', 'International', 'Mobilitas internasional, exchange, visa, KITAS, dan study permit.', 'International mobility, exchange, visa, KITAS, and study permit services.'],
            'sarana-prasarana' => ['Sarana dan Prasarana', 'Facilities and Infrastructure', 'Informasi fasilitas kampus, aksesibilitas, ruang, dan dukungan sarana prasarana.', 'Campus facilities, accessibility, rooms, and infrastructure support.'],
        ];

        DB::transaction(function () use ($categories): void {
            foreach ($categories as $slug => $values) {
                DB::table('service_categories')->updateOrInsert(
                    ['slug' => $slug],
                    ['name' => $values[0], 'name_en' => $values[1], 'description' => $values[2], 'description_en' => $values[3], 'sort_order' => array_search($slug, array_keys($categories), true), 'is_featured' => true, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            $aliases = [
                'akademik-mahasiswa' => 'mahasiswa-calon-mahasiswa-alumni',
                'mahasiswa-baru-smup' => 'mahasiswa-calon-mahasiswa-alumni',
                'dosen-tendik' => 'dosen-tenaga-kependidikan',
                'paus-ti' => 'teknologi-informasi',
                'layanan-internasional' => 'internasional',
                'layanan-disabilitas' => 'sarana-prasarana',
            ];

            foreach ($aliases as $oldSlug => $newSlug) {
                $oldId = DB::table('service_categories')->where('slug', $oldSlug)->value('id');
                $newId = DB::table('service_categories')->where('slug', $newSlug)->value('id');
                if ($oldId && $newId) {
                    DB::table('services')->where('service_category_id', $oldId)->update(['service_category_id' => $newId]);
                    DB::table('service_categories')->where('id', $oldId)->delete();
                }
            }
        });
    }

    public function down(): void
    {
        // Data is intentionally retained because reversing merged categories could misclassify services.
    }
};
