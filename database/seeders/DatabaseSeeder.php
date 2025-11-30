<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


//use Database\Seeders\UserRoleSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Pastikan Anda hanya memanggil seeder yang sudah benar
            InitialUserSeeder::class, // ⭐ Panggil seeder baru di sini
            AgencySeeder::class
            // UserRoleSeeder::class, // <-- Pastikan ini DIHAPUS atau DIKOMEN
        ]);
        
    }
}
