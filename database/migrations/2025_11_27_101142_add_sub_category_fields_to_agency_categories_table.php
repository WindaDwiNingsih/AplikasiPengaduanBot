<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agency_categories', function (Blueprint $table) {
            // Hapus constraint unique yang lama
            $table->dropUnique(['name', 'agency_id']);

            // Tambah kolom untuk parent category (hierarki)
            $table->foreignId('parent_id')->nullable()->constrained('agency_categories')->onDelete('cascade');
            $table->string('type')->default('sub_category'); // 'main_category' atau 'sub_category'

            // Unique constraint baru: name harus unique per agency dan parent
            $table->unique(['name', 'agency_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::table('agency_categories', function (Blueprint $table) {
            $table->dropUnique(['name', 'agency_id', 'parent_id']);
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'type']);

            // Kembalikan constraint lama
            $table->unique(['name', 'agency_id']);
        });
    }
};
