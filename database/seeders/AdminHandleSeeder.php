<?php

namespace Database\Seeders;

use App\Models\AdminHandle;
use Illuminate\Database\Seeder;

class AdminHandleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminHandles = [
            ['name' => 'Bangchan'],
            ['name' => 'Leeknow'],
        ];

        foreach ($adminHandles as $handle) {
            AdminHandle::firstOrCreate(
                ['name' => $handle['name']],
                $handle
            );
        }
    }
}