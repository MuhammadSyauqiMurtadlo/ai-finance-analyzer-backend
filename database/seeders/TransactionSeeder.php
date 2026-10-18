<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Data contoh 6 bulan terakhir. Dijalankan manual:
     *   php artisan db:seed --class=TransactionSeeder
     *
     * PERINGATAN: tabel transactions dikosongkan dulu supaya hasilnya konsisten.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('Seeder data contoh tidak boleh dijalankan di production.');

            return;
        }

        mt_srand(42); // hasil acak yang sama setiap dijalankan
        Transaction::truncate();

        $today = Carbon::today();
        $rows = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->startOfMonth()->subMonths($i);
            $maxDay = $i === 0 ? $today->day : $month->daysInMonth;

            $add = function (string $type, string $category, int $min, int $max, int $day, string $note) use (&$rows, $month, $maxDay) {
                if ($day > $maxDay) {
                    return; // jangan buat transaksi di masa depan
                }

                $rows[] = [
                    'type' => $type,
                    'amount' => (int) (round(mt_rand($min, $max) / 1000) * 1000),
                    'category' => $category,
                    'date' => $month->copy()->day($day)->toDateString(),
                    'note' => $note,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            };

            // Income
            $add('income', 'Salary', 7_500_000, 8_500_000, 1, 'Gaji bulanan');
            if (in_array($i, [3, 1], true)) {
                $add('income', 'Bonus', 500_000, 1_500_000, 15, 'Bonus proyek');
            }
            if ($i % 2 === 0) {
                $add('income', 'Other Income', 200_000, 800_000, 12, 'Freelance kecil-kecilan');
            }

            // Expense
            foreach ([2, 5, 9, 14, 19, 23, 27] as $day) {
                $add('expense', 'Food', 20_000, 75_000, $day, 'Makan');
            }
            foreach ([3, 10, 17, 24] as $day) {
                $add('expense', 'Transportation', 15_000, 60_000, $day, 'Bensin / ojek');
            }
            foreach ([8, 20] as $day) {
                $add('expense', 'Shopping', 100_000, 450_000, $day, 'Belanja kebutuhan');
            }
            foreach ([13, 26] as $day) {
                $add('expense', 'Entertainment', 50_000, 200_000, $day, 'Hiburan');
            }
            $add('expense', 'Bills', 300_000, 400_000, 5, 'Internet');
            $add('expense', 'Bills', 150_000, 300_000, 7, 'Listrik');
            $add('expense', 'Other Expense', 50_000, 150_000, 16, 'Lain-lain');
        }

        Transaction::insert($rows);

        $this->command->info(count($rows).' transaksi contoh dibuat.');
    }
}
