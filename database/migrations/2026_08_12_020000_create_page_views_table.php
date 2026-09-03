<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500);
            $table->string('route_name')->nullable();
            $table->string('locale', 5)->default('id');
            $table->string('referrer_host')->nullable();
            $table->string('visitor_hash', 64);
            $table->string('ip_hash', 64)->nullable();
            $table->string('device_type', 20)->default('desktop');
            $table->timestamp('viewed_at')->useCurrent();

            $table->index('viewed_at');
            $table->index(['route_name', 'viewed_at']);
            $table->index(['visitor_hash', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
