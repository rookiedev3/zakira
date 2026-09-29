<?php

namespace Database\Seeders;

use App\Models\CustomerService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CsSeeder extends Seeder
{
    public function run(): void
    {
        CustomerService::firstOrCreate(
        [
        'name' => 'Chyntia',
        'phone_number' => '0871829891',
        'order' => '0',
        'status' => 'aktif',
        'is_floating_whatsapp' => false,
        ]
        );
    }
}