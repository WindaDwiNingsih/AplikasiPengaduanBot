<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ...
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom 'role' dengan nilai default 'admin_dinas'
            // Kita gunakan ENUM untuk membatasi nilai yang mungkin.
            $table->enum('role', ['superadmin', 'admin_dinas'])
                ->default('admin_dinas')
                ->after('email'); // Setelah kolom email
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
    
};
