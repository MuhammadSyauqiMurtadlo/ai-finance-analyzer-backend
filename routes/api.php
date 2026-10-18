<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', DashboardController::class);
Route::apiResource('transactions', TransactionController::class);
