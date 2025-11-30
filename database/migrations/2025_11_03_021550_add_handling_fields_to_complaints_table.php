<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Untuk Laporan Kinerja Agen: mencatat siapa yang menangani
            $table->unsignedBigInteger('assigned_to_user_id')->nullable()->after('location');
            // Untuk Laporan Histori: mencatat kapan selesai
            $table->timestamp('resolved_at')->nullable()->after('status');

            $table->foreign('assigned_to_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_user_id']);
            $table->dropColumn('assigned_to_user_id');
            $table->dropColumn('resolved_at');
        });
    }
};
