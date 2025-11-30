<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agency_categories', function (Blueprint $table) {
            // Ubah agency_id menjadi nullable
            $table->foreignId('agency_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('agency_categories', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable(false)->change();
        });
    }
};
