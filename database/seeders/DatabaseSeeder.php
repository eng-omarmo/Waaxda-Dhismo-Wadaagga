<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'contact_phone' => '+252-61-0000000',
                'contact_address' => 'Xamar, Mogadishu',
                'password' => 'Admin@123456!',
                'email_verified_at' => now(),
                'role' => 'admin',
                'active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'first_name' => 'Citizen',
                'last_name' => 'User',
                'contact_phone' => '+252-61-0000001',
                'contact_address' => 'Xamar, Mogadishu',
                'password' => 'User@123456!',
                'email_verified_at' => now(),
                'role' => 'user',
                'active' => true,
            ]
        );
    }
}
