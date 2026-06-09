<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:6,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:6,1');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Clients (کەسەکان)
    Route::resource('clients', ClientController::class);

    // Transactions (مامەڵەکان)
    Route::resource('transactions', TransactionController::class)->except(['edit', 'update']);
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::get('/transactions/{transaction}/print', [TransactionController::class, 'printReceipt'])->name('transactions.print');

    // Exchange Rates (ڕێژەی گۆڕینی دراو)
    Route::resource('exchange-rates', ExchangeRateController::class)->only(['index', 'store', 'destroy']);
    Route::get('/exchange-rates/current', [ExchangeRateController::class, 'current'])->name('exchange-rates.current');

    // Reports (ڕاپۆرتەکان)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/client/{client}', [ReportController::class, 'clientReport'])->name('reports.client');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/client/{client}/export', [ReportController::class, 'exportClientExcel'])->name('reports.client.export');
});
