<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankAccounts = [
            [
                'bank_name'           => 'BCA',
                'account_number'      => '1234567890',
                'account_holder_name' => 'PT Zakira Utama',
                'status'              => 'aktif',
            ],
            [
                'bank_name'           => 'Mandiri',
                'account_number'      => '0987654321',
                'account_holder_name' => 'PT Zakira Utama',
                'status'              => 'aktif',
            ],
        ];

        foreach ($bankAccounts as $account) {
            BankAccount::firstOrCreate(
                ['account_number' => $account['account_number']],
                $account
            );
        }
    }
}