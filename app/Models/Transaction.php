<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    public const CATEGORIES = [
        self::TYPE_INCOME => ['Salary', 'Bonus', 'Other Income'],
        self::TYPE_EXPENSE => [
            'Food',
            'Transportation',
            'Shopping',
            'Entertainment',
            'Bills',
            'Other Expense',
        ],
    ];

    protected $fillable = ['type', 'amount', 'category', 'date', 'note'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date:Y-m-d',
        ];
    }
}