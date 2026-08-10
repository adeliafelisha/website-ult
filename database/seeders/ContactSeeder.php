<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            ['WhatsApp Admin ULT', 'whatsapp', '+62 800-0000-0000', 'https://wa.me/6280000000000', 'Nomor contoh—ganti dengan nomor resmi melalui Filament.'],
            ['Helpdesk Unpad', 'helpdesk', 'helpdesk.unpad.ac.id', 'https://helpdesk.unpad.ac.id/', 'Kanal bantuan resmi Universitas Padjadjaran.'],
            ['Instagram ULT Unpad', 'instagram', '@ult_unpad', 'https://www.instagram.com/ult_unpad?igsh=aThjeHo1YmlmcTl6', 'Ikuti informasi dan aktivitas terbaru ULT Unpad.'],
            ['TikTok ULT Unpad', 'tiktok', '@ult_unpad', 'https://www.tiktok.com/@ult_unpad', 'Temukan konten layanan dan informasi singkat ULT Unpad.'],
            ['Email ULT Unpad', 'email', 'ult@unpad.ac.id', 'mailto:ult@unpad.ac.id', 'Alamat contoh—verifikasi email resmi sebelum publikasi.'],
        ];

        foreach ($contacts as $i => $contact) {
            Contact::updateOrCreate(['label' => $contact[0]], [
                'type' => $contact[1], 'value' => $contact[2], 'url' => $contact[3],
                'description' => $contact[4], 'sort_order' => $i, 'is_published' => true,
            ]);
        }
    }
}
