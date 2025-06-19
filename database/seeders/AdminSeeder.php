<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah user dengan email admin@school.com sudah ada
        if (!User::where('email', 'admin@school.com')->exists()) {
            User::create([
                'name' => 'Admin Utama',
                'email' => 'admin@school.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }
    }
}