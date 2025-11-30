<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use Illuminate\Support\Facades\DB;

class SystemAgencySeeder extends Seeder
{
    public function run(): void
    {
        // Non-aktifkan foreign key check sementara (untuk MySQL)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Buat atau update agency system
        $systemAgency = Agency::updateOrCreate(
            ['id' => 999],
            [
                'name' => 'System - Kategori Utama',
                'description' => 'Agency khusus untuk menyimpan kategori utama sistem',
                'is_active' => false, // Non-aktif karena hanya untuk sistem
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("System agency created/updated: {$systemAgency->name} (ID: {$systemAgency->id})");
    }
}
