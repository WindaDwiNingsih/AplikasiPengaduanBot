<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_histories', function (Blueprint $table) {
            $table->id();

            // FK ke Pengaduan (Wajib)
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');

            // FK ke User (Siapa yang mengubah)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Status dan Catatan
            $table->string('old_status', 20)->nullable();
            $table->string('new_status', 20);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_histories');
    }
};
