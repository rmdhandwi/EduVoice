<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Default Kepala Dinas
        User::updateOrCreate(
            ['email' => 'kepala@example.com'],
            [
                'name' => 'Kepala Dinas',
                'username' => 'kepala',
                'password' => Hash::make('password123'),
                'role' => 'kepala',
            ]
        );
    }
}
