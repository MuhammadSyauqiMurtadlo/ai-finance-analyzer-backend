<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $transactions = Transaction::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $transactions]);
    }

    public function store(TransactionRequest $request): JsonResponse
    {
        $transaction = Transaction::create($request->validated());

        return response()->json(['data' => $transaction], 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json(['data' => $transaction]);
    }

    public function update(TransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $transaction->update($request->validated());

        return response()->json(['data' => $transaction->fresh()]);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json(null, 204);
    }
}
