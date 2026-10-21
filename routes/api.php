<?php

use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', DashboardController::class);
Route::apiResource('transactions', TransactionController::class);

// Dibatasi 5 request/menit per IP: setiap panggilan AI berbiaya, dan aplikasi ini tanpa login.
Route::post('ai/analyze', AiAnalysisController::class)->middleware('throttle:5,1');
