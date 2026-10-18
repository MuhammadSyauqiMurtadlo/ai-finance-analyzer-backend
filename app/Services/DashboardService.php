<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class DashboardService
{
    private const CHART_MONTHS = 6;

    private const RECENT_LIMIT = 10;

    public function get(): array
    {
        return [
            'summary' => $this->summary(),
            'income_vs_expense' => $this->incomeVsExpense(),
            'expense_by_category' => $this->byCategory(Transaction::TYPE_EXPENSE),
            'income_by_category' => $this->byCategory(Transaction::TYPE_INCOME),
            'recent_transactions' => $this->recentTransactions(),
        ];
    }

    private function summary(): array
    {
        $totals = Transaction::query()
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $income = (float) ($totals[Transaction::TYPE_INCOME] ?? 0);
        $expense = (float) ($totals[Transaction::TYPE_EXPENSE] ?? 0);

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'balance' => round($income - $expense, 2),
        ];
    }

    /**
     * Income vs Expense per bulan untuk 6 bulan terakhir (termasuk bulan ini).
     * Bulan tanpa transaksi tetap dikembalikan dengan nilai 0 supaya chart tidak bolong.
     */
    private function incomeVsExpense(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(self::CHART_MONTHS - 1);

        $rows = Transaction::query()
            ->where('date', '>=', $start->toDateString())
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, type, SUM(amount) as total")
            ->groupBy('month', 'type')
            ->get();

        return collect(range(0, self::CHART_MONTHS - 1))
            ->map(function (int $i) use ($start, $rows) {
                $month = $start->copy()->addMonths($i)->format('Y-m');

                $totalOf = fn (string $type) => (float) (
                    $rows->first(fn ($r) => $r->month === $month && $r->type === $type)?->total ?? 0
                );

                return [
                    'month' => $month,
                    'income' => $totalOf(Transaction::TYPE_INCOME),
                    'expense' => $totalOf(Transaction::TYPE_EXPENSE),
                ];
            })
            ->all();
    }

    private function byCategory(string $type): array
    {
        return Transaction::query()
            ->where('type', $type)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total' => (float) $row->total,
            ])
            ->all();
    }

    private function recentTransactions()
    {
        return Transaction::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(self::RECENT_LIMIT)
            ->get();
    }
}
