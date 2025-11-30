<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('sub_category')->nullable()->after('category');
            $table->foreignId('agency_sub_category_id')->nullable()->constrained('agency_categories')->onDelete('set null')->after('agency_id');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['agency_sub_category_id']);
            $table->dropColumn(['sub_category', 'agency_sub_category_id']);
        });
    }
};
