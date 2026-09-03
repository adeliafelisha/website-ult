<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->text('description_en')->nullable()->after('description');
        });
        Schema::table('services', function (Blueprint $table) {
            foreach (['title', 'audience', 'cta_label', 'location', 'service_hours', 'process_time', 'fee', 'responsible_unit', 'content_owner', 'seo_description'] as $column) {
                $table->string($column.'_en')->nullable()->after($column);
            }
            foreach (['summary', 'requirements', 'documents'] as $column) {
                $table->text($column.'_en')->nullable()->after($column);
            }
            $table->longText('procedure_en')->nullable()->after('procedure');
        });
        Schema::table('articles', function (Blueprint $table) {
            foreach (['title', 'category', 'author', 'content_owner', 'seo_description'] as $column) {
                $table->string($column.'_en')->nullable()->after($column);
            }
            $table->text('excerpt_en')->nullable()->after('excerpt');
            $table->longText('content_en')->nullable()->after('content');
        });
        Schema::table('faqs', function (Blueprint $table) {
            foreach (['question', 'category', 'audience'] as $column) {
                $table->string($column.'_en')->nullable()->after($column);
            }
            $table->longText('answer_en')->nullable()->after('answer');
        });
        Schema::table('quick_links', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->text('description_en')->nullable()->after('description');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('label_en')->nullable()->after('label');
            $table->string('value_en')->nullable()->after('value');
            $table->text('description_en')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', fn (Blueprint $t) => $t->dropColumn(['name_en', 'description_en']));
        Schema::table('services', fn (Blueprint $t) => $t->dropColumn(['title_en', 'summary_en', 'audience_en', 'requirements_en', 'documents_en', 'procedure_en', 'cta_label_en', 'location_en', 'service_hours_en', 'process_time_en', 'fee_en', 'responsible_unit_en', 'content_owner_en', 'seo_description_en']));
        Schema::table('articles', fn (Blueprint $t) => $t->dropColumn(['title_en', 'category_en', 'excerpt_en', 'content_en', 'author_en', 'content_owner_en', 'seo_description_en']));
        Schema::table('faqs', fn (Blueprint $t) => $t->dropColumn(['question_en', 'answer_en', 'category_en', 'audience_en']));
        Schema::table('quick_links', fn (Blueprint $t) => $t->dropColumn(['name_en', 'description_en']));
        Schema::table('contacts', fn (Blueprint $t) => $t->dropColumn(['label_en', 'value_en', 'description_en']));
    }
};
