<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cnpj_queries', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj', 14);
            $table->string('razao_social')->nullable();
            $table->string('source', 10)->default('web');
            $table->boolean('success')->default(false);
            $table->string('error_message')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('queried_at');

            $table->index('cnpj');
            $table->index('queried_at');
            $table->index(['cnpj', 'queried_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cnpj_queries');
    }
};
