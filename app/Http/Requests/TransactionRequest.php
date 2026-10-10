<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // tidak ada auth di aplikasi ini
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in([
                Transaction::TYPE_INCOME,
                Transaction::TYPE_EXPENSE,
            ])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'category' => ['required', 'string', function ($attribute, $value, $fail) {
                $type = $this->input('type');
                $validCategories = Transaction::CATEGORIES[$type] ?? [];

                if (! in_array($value, $validCategories, true)) {
                    $fail("Kategori '{$value}' tidak valid untuk tipe '{$type}'.");
                }
            }],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
