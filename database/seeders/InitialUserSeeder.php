<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

class InitialUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat User Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('Password123!'), // Password: 'password'
            'role' => 'superadmin',
        ]);

        // 2. Buat User Admin Dinas
        User::create([
            'name' => 'Admin Dinas Default',
            'email' => 'admindinas@gmail.com',
            'password' => Hash::make('Password123!'), // Password: 'password'
            'role' => 'admin_dinas',
        ]);

        echo "Seeder berhasil dijalankan. 2 user telah ditambahkan.\n";
    }
}
