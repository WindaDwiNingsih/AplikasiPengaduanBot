<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agency_categories', function (Blueprint $table) {
            // CUKUP TAMBAH KOLOM INI SAJA
            $table->string('main_category')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('agency_categories', function (Blueprint $table) {
            $table->dropColumn('main_category');
        });
    }
};
