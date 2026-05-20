<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    public function run(): void {
        $adminEmails = array_map('trim', explode(',', config('nutrigo.admin_emails', 'admin@nutrigo.id')));

        foreach ($adminEmails as $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'                 => 'Admin NutriGo',
                    'password'             => Hash::make('Admin@123'),
                    'is_admin'             => true,
                    'onboarding_completed' => true,
                ]
            );
        }

        $this->command->info('✅ Admin berhasil dibuat!');
    }
}