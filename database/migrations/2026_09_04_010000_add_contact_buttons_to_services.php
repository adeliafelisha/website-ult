<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->json('contact_buttons')->nullable()->after('cta_url');
        });

        $whatsAppUrl = DB::table('contacts')
            ->where('type', 'whatsapp')
            ->where('is_published', true)
            ->value('url') ?: 'https://wa.me/6280000000000';

        DB::table('services')->orderBy('id')->eachById(function (object $service) use ($whatsAppUrl): void {
            $buttons = [[
                'label' => 'Hubungi Admin ULT',
                'label_en' => 'Contact ULT Admin',
                'channel' => 'whatsapp',
                'url' => $whatsAppUrl,
            ]];

            if ($service->cta_url && $service->cta_url !== $whatsAppUrl) {
                $buttons[] = [
                    'label' => $service->cta_label ?: 'Buka kanal layanan',
                    'label_en' => $service->cta_label_en ?: 'Open service channel',
                    'channel' => 'other',
                    'url' => $service->cta_url,
                ];
            }

            DB::table('services')->where('id', $service->id)->update([
                'contact_buttons' => json_encode($buttons, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('contact_buttons');
        });
    }
};
