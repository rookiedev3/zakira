<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Zakira',
                'password' => Hash::make('admin1122'),
                'role' => 'admin',
                'status' => 'aktif',
                'customer_type' => null,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'member@gmail.com'],
            [
                'name' => 'Member Zakira',
                'password' => Hash::make('member1122'),
                'role' => 'customer',
                'status' => 'aktif',
                'customer_type' => 'member',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer Umum',
                'password' => Hash::make('customer1122'),
                'role' => 'customer',
                'status' => 'aktif',
                'customer_type' => 'umum',
                'email_verified_at' => null,
            ]
        );
    }
}