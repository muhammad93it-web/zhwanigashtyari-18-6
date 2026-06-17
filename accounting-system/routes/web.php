<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractorPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LaborPaymentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialMovementController;
use App\Http\Controllers\PrintCenterController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerController;
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

    // ===== دارایی / Finance =====
    Route::middleware('perm:finance')->group(function () {
        Route::resource('incomes', IncomeController::class);
        Route::resource('expenses', ExpenseController::class);
        Route::resource('debts', DebtController::class);
        Route::post('/debts/{debt}/mark-paid', [DebtController::class, 'markPaid'])->name('debts.mark-paid');
    });

    // ===== کڕین و فرۆشتن و کۆگا / Trading & Inventory =====
    Route::middleware('perm:trading')->group(function () {
        Route::resource('materials', MaterialController::class);
        Route::get('/materials-buy', [MaterialMovementController::class, 'create'])->defaults('type', 'purchase')->name('materials.buy');
        Route::get('/materials-sell', [MaterialMovementController::class, 'create'])->defaults('type', 'sale')->name('materials.sell');
        Route::post('/material-movements', [MaterialMovementController::class, 'store'])->name('movements.store');
        Route::delete('/material-movements/{movement}', [MaterialMovementController::class, 'destroy'])->name('movements.destroy');
    });

    // ===== پڕۆژەکان / Projects =====
    Route::middleware('perm:projects')->group(function () {
        Route::resource('projects', ProjectController::class);
    });

    // ===== دابینکەران و کڕینی وەسڵ / Suppliers & Purchase Invoices =====
    Route::middleware('perm:suppliers')->group(function () {
        Route::resource('suppliers', SupplierController::class);
        Route::get('/suppliers/{supplier}/pay', [SupplierPaymentController::class, 'create'])->name('suppliers.pay');
        Route::post('/suppliers/{supplier}/pay', [SupplierPaymentController::class, 'store'])->name('suppliers.pay.store');
        Route::resource('purchase-invoices', PurchaseInvoiceController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('/purchase-invoices/{purchase_invoice}/print', [PurchaseInvoiceController::class, 'print'])->name('purchase-invoices.print');
        Route::get('/purchase-invoices/{purchase_invoice}/export-excel', [PurchaseInvoiceController::class, 'exportExcel'])->name('purchase-invoices.export-excel');
        Route::get('/purchase-invoices/{purchase_invoice}/export-word', [PurchaseInvoiceController::class, 'exportWord'])->name('purchase-invoices.export-word');
    });

    // ===== کرێی کار و کرێکاران / Labor =====
    Route::middleware('perm:labor')->group(function () {
        Route::resource('workers', WorkerController::class);
        Route::resource('labor-payments', LaborPaymentController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // ===== وەستا / Contractors =====
    Route::middleware('perm:contractors')->group(function () {
        Route::resource('contractors', ContractorController::class);
        Route::resource('contractor-payments', ContractorPaymentController::class)->only(['index', 'create', 'store', 'destroy']);
    });

    // ===== ڕاپۆرتەکان / Reports =====
    Route::middleware('perm:reports')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
        Route::get('/reports/project-cost', [ReportController::class, 'projectCost'])->name('reports.project-cost');
        Route::get('/reports/client/{client}', [ReportController::class, 'clientReport'])->name('reports.client');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/client/{client}/export', [ReportController::class, 'exportClientExcel'])->name('reports.client.export');
    });

    // ===== کارگێڕی / Administration =====
    Route::middleware('perm:documents')->group(function () {
        Route::resource('documents', DocumentController::class);
        Route::get('/documents/{document}/print', [DocumentController::class, 'print'])->name('documents.print');
    });
    Route::middleware('perm:print_center')->group(function () {
        Route::get('/print-center', [PrintCenterController::class, 'index'])->name('print-center.index');
        Route::get('/print-center/print', [PrintCenterController::class, 'print'])->name('print-center.print');
    });

    // ===== ڕێکخستن / Settings =====
    Route::middleware('perm:clients')->group(function () {
        Route::resource('clients', ClientController::class);
    });

    Route::middleware('perm:transactions')->group(function () {
        Route::resource('transactions', TransactionController::class)->except(['edit', 'update']);
        Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
        Route::get('/transactions/{transaction}/print', [TransactionController::class, 'printReceipt'])->name('transactions.print');
    });

    Route::middleware('perm:exchange_rates')->group(function () {
        Route::resource('exchange-rates', ExchangeRateController::class)->only(['index', 'store', 'destroy']);
        Route::get('/exchange-rates/current', [ExchangeRateController::class, 'current'])->name('exchange-rates.current');
    });

    // ===== بەڕێوەبردنی بەکارهێنەران / User management (admin only) =====
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('users/{user}/password', [UserController::class, 'editPassword'])->name('users.password.edit');
        Route::put('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password.update');
    });
});
