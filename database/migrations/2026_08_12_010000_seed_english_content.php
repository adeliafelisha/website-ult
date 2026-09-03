<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cats = ['akademik-mahasiswa' => ['Student Academic Services', 'Registration, tuition, course registration, and student documents.'], 'mahasiswa-baru-smup' => ['New Students & SMUP', 'Admission, verification, student ID numbers, and student cards.'], 'dosen-tendik' => ['Lecturers & Staff', 'Services for lecturers and education staff.'], 'layanan-internasional' => ['International Services', 'Exchange, visa, KITAS, and study permit services.'], 'paus-ti' => ['PAuS & Information Technology', 'PAuS accounts, SIAT, PINTAS, and LiVE.'], 'layanan-disabilitas' => ['Disability Services', 'Accessible facilities and inclusive assistance.']];
        foreach ($cats as $slug => $v) {
            DB::table('service_categories')->where('slug', $slug)->update(['name_en' => $v[0], 'description_en' => $v[1]]);
        }
        $services = ['penggantian-ktm' => ['Lost or Damaged Student Card Replacement', 'Requirements and steps to replace a student card.', 'Active students'], 'informasi-ukt-registrasi' => ['Tuition and Registration Information', 'Information about tuition payments, re-registration, and official support channels.', 'Active students'], 'registrasi-mahasiswa-baru' => ['New Student Registration', 'Registration, document verification, and admissions guidance.', 'New students'], 'pendampingan-layanan-disabilitas' => ['Disability Service Assistance', 'Accessible facilities and campus service assistance.', 'Users with disabilities'], 'bantuan-akun-paus' => ['PAuS Account Support', 'Support for account access, password recovery, and system issues.', 'Students, lecturers & staff'], 'informasi-mahasiswa-internasional' => ['International Student Information', 'Information routing for exchange, visa, KITAS, and study permits.', 'International users']];
        foreach ($services as $slug => $v) {
            DB::table('services')->where('slug', $slug)->update(['title_en' => $v[0], 'summary_en' => $v[1], 'audience_en' => $v[2], 'cta_label_en' => 'Open Official Service']);
        }
        $articles = ['panduan-menemukan-layanan' => ['Quick Guide to Finding Campus Services', 'Guide', 'Start with your needs, then follow the primary action for each service.', 'Use search, select a service, review the requirements, then follow the action button to the official channel.'], 'kampus-inklusif-fasilitas-aksesibilitas' => ['Inclusive Campus: Accessibility Facilities', 'Accessibility', 'Learn about accessibility support and how to request assistance before visiting.', 'Contact an official channel before your visit so accessibility support can be prepared.'], 'persiapan-administrasi-mahasiswa-baru' => ['New Student Administration Preparation', 'New Students', 'A concise checklist for the registration process.', 'Keep identity data consistent and complete the process through the official SMUP portal.']];
        foreach ($articles as $slug => $v) {
            DB::table('articles')->where('slug', $slug)->update(['title_en' => $v[0], 'category_en' => $v[1], 'excerpt_en' => $v[2], 'content_en' => '<p>'.$v[3].'</p>', 'author_en' => 'ULT Unpad Team', 'content_owner_en' => 'ULT Unpad']);
        }
        $faqs = ['Bagaimana cara menemukan layanan yang tepat?' => ['How do I find the right service?', 'Use global search or select a category based on your needs. Each service provides a clear next step.', 'General'], 'Apakah website ULT membuat tiket pengaduan?' => ['Does the ULT website create support tickets?', 'No. The ULT website is an information portal that directs you to official systems or channels.', 'General'], 'Apa yang dilakukan jika tautan eksternal bermasalah?' => ['What should I do if an external link is unavailable?', 'Open the Contact page, choose an alternative official channel, and provide the service name and issue.', 'Technical'], 'Bagaimana meminta pendampingan aksesibilitas?' => ['How do I request accessibility assistance?', 'Open Disability Services and contact ULT before your visit.', 'Accessibility']];
        foreach ($faqs as $q => $v) {
            DB::table('faqs')->where('question', $q)->update(['question_en' => $v[0], 'answer_en' => $v[1], 'category_en' => $v[2]]);
        }
    }

    public function down(): void {}
};
