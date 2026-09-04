<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('external_url')->nullable()->after('content_en');
            $table->string('external_label')->nullable()->after('external_url');
            $table->string('external_label_en')->nullable()->after('external_label');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->string('external_url')->nullable()->after('answer_en');
            $table->string('external_label')->nullable()->after('external_url');
            $table->string('external_label_en')->nullable()->after('external_label');
        });

        Schema::create('satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->decimal('quarter_1_score', 5, 2)->nullable();
            $table->decimal('quarter_2_score', 5, 2)->nullable();
            $table->decimal('quarter_3_score', 5, 2)->nullable();
            $table->decimal('quarter_4_score', 5, 2)->nullable();
            $table->string('source_url')->nullable();
            $table->string('questionnaire_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satisfaction_surveys');
        Schema::table('faqs', fn (Blueprint $table) => $table->dropColumn(['external_url', 'external_label', 'external_label_en']));
        Schema::table('articles', fn (Blueprint $table) => $table->dropColumn(['external_url', 'external_label', 'external_label_en']));
    }
};
