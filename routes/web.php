<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashBookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpiryController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Cache Clear Route (For Live Server / cPanel)
Route::get('clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return '<div style="font-family:sans-serif; text-align:center; padding:50px;">
        <h2 style="color:#10b981;">✅ All Laravel Caches Cleared Successfully!</h2>
        <p>Config, Cache, Route, View and Optimize caches cleared.</p>
        <a href="'.url('/').'" style="display:inline-block; margin-top:15px; padding:10px 20px; background:#10b981; color:#fff; text-decoration:none; border-radius:5px; font-weight:bold;">Go to Dashboard</a>
    </div>';
})->name('clear.cache');

// Run Migrations & Seeders Route (For Live Server / cPanel)
Route::get('run-migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);
    return '<div style="font-family:sans-serif; text-align:center; padding:50px;">
        <h2 style="color:#10b981;">✅ Database Migrated & Seeded Successfully!</h2>
        <p>All tables created and initial demo data populated.</p>
        <a href="'.url('/').'" style="display:inline-block; margin-top:15px; padding:10px 20px; background:#10b981; color:#fff; text-decoration:none; border-radius:5px; font-weight:bold;">Go to Dashboard</a>
    </div>';
})->name('run.migrate');

// Auth & Language Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('lang/{locale}', [AuthController::class, 'switchLanguage'])->name('lang.switch');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Products & Master Data
    Route::get('products/search-api', [ProductController::class, 'searchApi'])->name('products.search-api');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('brands', BrandController::class)->except(['create', 'show', 'edit']);
    Route::resource('units', UnitController::class)->except(['create', 'show', 'edit']);

    // Suppliers & Customers
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/{supplier}/payment', [SupplierController::class, 'addPayment'])->name('suppliers.payment');

    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/payment', [CustomerController::class, 'addPayment'])->name('customers.payment');

    // Employees & Salaries
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/salary', [EmployeeController::class, 'paySalary'])->name('employees.salary');

    // Payment Methods
    Route::resource('payment-methods', PaymentMethodController::class)->except(['create', 'edit', 'destroy']);

    // Purchases & Returns
    Route::get('purchases/{purchase}/print', [PurchaseController::class, 'printInvoice'])->name('purchases.print');
    Route::get('purchases/{purchase}/return', [PurchaseController::class, 'returnView'])->name('purchases.return');
    Route::post('purchases/{purchase}/return', [PurchaseController::class, 'storeReturn'])->name('purchases.return.store');
    Route::resource('purchases', PurchaseController::class);

    // Sales & POS & Returns
    Route::get('pos', [SaleController::class, 'pos'])->name('sales.pos');
    Route::get('sales/{sale}/print', [SaleController::class, 'printInvoice'])->name('sales.print');
    Route::get('sales/{sale}/return', [SaleController::class, 'returnView'])->name('sales.return');
    Route::post('sales/{sale}/return', [SaleController::class, 'storeReturn'])->name('sales.return.store');
    Route::resource('sales', SaleController::class);

    // Stock Management
    Route::get('stock/overview', [StockController::class, 'overview'])->name('stock.overview');
    Route::get('stock/adjustments', [StockController::class, 'adjustments'])->name('stock.adjustments');
    Route::post('stock/adjustments', [StockController::class, 'storeAdjustment'])->name('stock.adjustments.store');
    Route::get('stock/transfers', [StockController::class, 'transfers'])->name('stock.transfers');
    Route::post('stock/transfers', [StockController::class, 'storeTransfer'])->name('stock.transfers.store');

    // Expiry Management
    Route::get('expiry', [ExpiryController::class, 'index'])->name('expiry.index');

    // Cash Book & Investments
    Route::get('cashbook', [CashBookController::class, 'index'])->name('cashbook.index');
    Route::post('cashbook', [CashBookController::class, 'storeManual'])->name('cashbook.store');

    Route::get('investments', [InvestmentController::class, 'index'])->name('investments.index');
    Route::post('investments', [InvestmentController::class, 'store'])->name('investments.store');

    // Expenses
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::post('expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');

    // Comprehensive Reports
    Route::get('reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('reports/purchases', [ReportController::class, 'purchaseReport'])->name('reports.purchases');
    Route::get('reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
    Route::get('reports/financial', [ReportController::class, 'financialReport'])->name('reports.financial');

    // Users, Roles, Settings
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});
