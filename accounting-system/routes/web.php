<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractorPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverTripLogController;
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
use App\Http\Controllers\SettingsController;
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
        Route::get('/statements', [SupplierController::class, 'statements'])->name('suppliers.statements');
        Route::post('/statements/go', [SupplierController::class, 'statementGo'])->name('suppliers.statement-go');
        Route::get('/suppliers/{supplier}/statement/print', [SupplierController::class, 'statementPrint'])->name('suppliers.statement-print');
        Route::get('/suppliers/{supplier}/statement/excel', [SupplierController::class, 'statementExcel'])->name('suppliers.statement-excel');
        Route::get('/suppliers/{supplier}/statement/word', [SupplierController::class, 'statementWord'])->name('suppliers.statement-word');
        Route::resource('suppliers', SupplierController::class);
        Route::get('/suppliers/{supplier}/pay', [SupplierPaymentController::class, 'create'])->name('suppliers.pay');
        Route::post('/suppliers/{supplier}/pay', [SupplierPaymentController::class, 'store'])->name('suppliers.pay.store');
        Route::resource('purchase-invoices', PurchaseInvoiceController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('/purchase-invoices/{purchase_invoice}/print', [PurchaseInvoiceController::class, 'print'])->name('purchase-invoices.print');
        Route::get('/purchase-invoices/{purchase_invoice}/export-excel', [PurchaseInvoiceController::class, 'exportExcel'])->name('purchase-invoices.export-excel');
        Route::get('/purchase-invoices/{purchase_invoice}/export-word', [PurchaseInvoiceController::class, 'exportWord'])->name('purchase-invoices.export-word');
    });

    // ===== گواستنەوە و شۆفێر / Drivers & Transportation =====
    Route::middleware('perm:drivers')->group(function () {
        Route::get('/driver-statements', [DriverController::class, 'statements'])->name('drivers.statements');
        Route::post('/driver-statements/go', [DriverController::class, 'statementGo'])->name('drivers.statement-go');
        Route::get('/drivers/{driver}/statement/print', [DriverController::class, 'statementPrint'])->name('drivers.statement-print');
        Route::get('/drivers/{driver}/statement/excel', [DriverController::class, 'statementExcel'])->name('drivers.statement-excel');
        Route::get('/drivers/{driver}/statement/word', [DriverController::class, 'statementWord'])->name('drivers.statement-word');
        Route::resource('drivers', DriverController::class);
        Route::resource('driver-trip-logs', DriverTripLogController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('/driver-trip-logs/{driver_trip_log}/print', [DriverTripLogController::class, 'print'])->name('driver-trip-logs.print');
        Route::get('/driver-trip-logs/{driver_trip_log}/export-excel', [DriverTripLogController::class, 'exportExcel'])->name('driver-trip-logs.export-excel');
        Route::get('/driver-trip-logs/{driver_trip_log}/export-word', [DriverTripLogController::class, 'exportWord'])->name('driver-trip-logs.export-word');
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
        // Advanced reports
        Route::get('/reports/advanced', [ReportController::class, 'advanced'])->name('reports.advanced');
        Route::get('/reports/advanced/excel', [ReportController::class, 'exportAdvancedExcel'])->name('reports.advanced.excel');
        Route::get('/reports/advanced/word', [ReportController::class, 'exportAdvancedWord'])->name('reports.advanced.word');
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
        // System Settings (admin only)
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/backup', [SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup/import', [SettingsController::class, 'importBackup'])->name('settings.backup.import');
    });
});
