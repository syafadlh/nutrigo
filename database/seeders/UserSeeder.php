<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User biasa
        User::updateOrCreate(
            ['email' => 'user@nutrigo.id'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('User@123'),
                'is_admin' => false,
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@nutrigo.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('Admin@123'),
                'is_admin' => true,
            ]
        );
    }
}