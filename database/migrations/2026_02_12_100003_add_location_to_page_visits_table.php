<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->string('country', 50)->nullable()->after('device_type');
            $table->string('state', 2)->nullable()->after('country');
            $table->string('city', 100)->nullable()->after('state');

            $table->index('state');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropIndex(['state']);
            $table->dropIndex(['city']);
            $table->dropColumn(['country', 'state', 'city']);
        });
    }
};
