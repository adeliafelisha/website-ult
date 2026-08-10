<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('sparkles');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->string('audience')->nullable();
            $table->text('requirements')->nullable();
            $table->text('documents')->nullable();
            $table->longText('procedure')->nullable();
            $table->enum('delivery_type', ['online', 'offline', 'hybrid'])->default('online');
            $table->string('cta_label')->default('Buka Sistem Layanan');
            $table->string('cta_url')->nullable();
            $table->string('location')->nullable();
            $table->string('service_hours')->nullable();
            $table->string('process_time')->nullable();
            $table->string('fee')->nullable();
            $table->string('responsible_unit')->nullable();
            $table->string('content_owner')->nullable();
            $table->string('keywords')->nullable();
            $table->string('language', 5)->default('id');
            $table->string('featured_image')->nullable();
            $table->string('seo_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['is_published', 'service_category_id']);
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Informasi');
            $table->text('excerpt');
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('author')->default('ULT Unpad');
            $table->string('content_owner')->nullable();
            $table->string('language', 5)->default('id');
            $table->string('keywords')->nullable();
            $table->string('seo_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['is_published', 'published_at']);
        });
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->longText('answer');
            $table->string('category')->default('Umum');
            $table->string('audience')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
        Schema::create('quick_links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->text('description')->nullable();
            $table->string('icon')->default('arrow-up-right');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('type');
            $table->string('value');
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
        Schema::create('search_events', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->unsignedInteger('result_count')->default(0);
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
            $table->index('query');
        });
        Schema::create('outbound_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->string('source')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_clicks');
        Schema::dropIfExists('search_events');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('quick_links');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
