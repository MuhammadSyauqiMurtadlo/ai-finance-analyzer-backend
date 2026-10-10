<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::query()->create([
            'type' => Transaction::TYPE_INCOME,
            'amount' => 8500000,
            'category' => 'Salary',
            'date' => now()->toDateString(),
            'note' => 'Monthly salary',
        ]);

        Transaction::query()->create([
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 125000,
            'category' => 'Food',
            'date' => now()->toDateString(),
            'note' => 'Lunch',
        ]);
    }
}
