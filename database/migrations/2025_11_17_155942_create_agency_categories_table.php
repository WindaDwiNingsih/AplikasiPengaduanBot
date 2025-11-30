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
        Schema::create('agency_categories', function (Blueprint $table) {
            // $table->id();
            // $table->string('name');
            // $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            // $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            // $table->timestamps();

            // // Unique constraint: nama kategori harus unik per dinas
            // $table->unique(['name', 'agency_id']);
            $table->dropUnique(['name', 'agency_id']);
            $table->foreignId('parent_id')->nullable()->constrained('agency_categories')->onDelete('cascade');
            $table->string('type')->default('sub_category'); // 'main_category' atau 'sub_category'
            $table->unique(['name', 'agency_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_categories', function(Blueprint $table){
                $table->dropUnique(['name', 'agency_id', 'parent_id']);
                $table->dropForeign(['parent_id']);
                $table->dropColumn(['parent_id', 'type']);

                // Kembalikan constraint lama
                $table->unique(['name', 'agency_id']);
        });
    }
};
