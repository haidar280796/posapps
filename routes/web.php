<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\MasterData\CategoryController;
use App\Http\Controllers\MasterData\CustomerController;
use App\Http\Controllers\MasterData\ProductController;
use App\Http\Controllers\MasterData\ProductPriceController;
use App\Http\Controllers\MasterData\UnitController;
use App\Http\Controllers\MasterData\WarehouseController;
use App\Http\Controllers\Transaksi\PointOfSaleController;
use App\Http\Controllers\Transaksi\ProductStockController;
use App\Http\Controllers\Transaksi\StockAdjustmentController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('dashboard/master-data')->middleware(['auth', 'verified'])->group(function () {
    // customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('md.customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('md.customers.create');
    Route::post('/customers/store', [CustomerController::class, 'store'])->name('md.customers.store');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('md.customers.edit');
    Route::patch('/customers/{id}/update', [CustomerController::class, 'update'])->name('md.customers.update');
    Route::delete('/customers/{id}/destroy', [CustomerController::class, 'destroy'])->name('md.customers.destroy');
    // categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('md.categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('md.categories.create');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('md.categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('md.categories.edit');
    Route::patch('/categories/{id}/update', [CategoryController::class, 'update'])->name('md.categories.update');
    Route::delete('/categories/{id}/destroy', [CategoryController::class, 'destroy'])->name('md.categories.destroy');
    // units
    Route::get('/units', [UnitController::class, 'index'])->name('md.units.index');
    Route::get('/units/create', [UnitController::class, 'create'])->name('md.units.create');
    Route::post('/units/store', [UnitController::class, 'store'])->name('md.units.store');
    Route::get('/units/{id}/edit', [UnitController::class, 'edit'])->name('md.units.edit');
    Route::patch('/units/{id}/update', [UnitController::class, 'update'])->name('md.units.update');
    Route::delete('/units/{id}/destroy', [UnitController::class, 'destroy'])->name('md.units.destroy');
    // products
    Route::get('/products', [ProductController::class, 'index'])->name('md.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('md.products.create');
    Route::post('/products/store', [ProductController::class, 'store'])->name('md.products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('md.products.edit');
    Route::patch('/products/{id}/update', [ProductController::class, 'update'])->name('md.products.update');
    Route::delete('/products/{id}/destroy', [ProductController::class, 'destroy'])->name('md.products.destroy');
    Route::patch('/products/{id}/clone', [ProductController::class, 'clone'])->name('md.products.clone');
    // productPrices
    Route::post('/products/{id}/prices/store', [ProductPriceController::class, 'store'])->name('md.product_prices.store');
    Route::patch('/products/{id}/prices/{priceId}/update', [ProductPriceController::class, 'update'])->name('md.product_prices.update');
    Route::delete('/products/{id}/prices/{priceId}/destroy', [ProductPriceController::class, 'destroy'])->name('md.product_prices.destroy');
    // warehouses
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('md.warehouses.index');
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('md.warehouses.create');
    Route::post('/warehouses/store', [WarehouseController::class, 'store'])->name('md.warehouses.store');
    Route::get('/warehouses/{id}/edit', [WarehouseController::class, 'edit'])->name('md.warehouses.edit');
    Route::patch('/warehouses/{id}/update', [WarehouseController::class, 'update'])->name('md.warehouses.update');
    Route::delete('/warehouses/{id}/destroy', [WarehouseController::class, 'destroy'])->name('md.warehouses.destroy');
});

Route::prefix('dashboard/transaction')->middleware(['auth', 'verified'])->group(function () {
    // stocks
    Route::get('/stocks', [ProductStockController::class, 'index'])->name('trn.product_stocks.index');
    Route::get('/stocks/create', [ProductStockController::class, 'create'])->name('trn.product_stocks.create');
    Route::post('/stocks/store', [ProductStockController::class, 'store'])->name('trn.product_stocks.store');
    Route::delete('/stocks/{id}/destroy', [ProductStockController::class, 'destroy'])->name('trn.product_stocks.destroy');
    // stock_adjustments
    Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])->name('trn.stock_adjustments.index');
    Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('trn.stock_adjustments.create');
    Route::post('/stock-adjustments/store', [StockAdjustmentController::class, 'store'])->name('trn.stock_adjustments.store');
    Route::delete('/stock-adjustments/{id}/destroy', [StockAdjustmentController::class, 'destroy'])->name('trn.stock_adjustments.destroy');
    // pos
    Route::get('/point-of-sales', [PointOfSaleController::class, 'index'])->name('trn.pos.index');
    Route::get('/point-of-sales/cashier/open', [PointOfSaleController::class, 'create'])->name('trn.pos.open');
    Route::post('/point-of-sales/store', [PointOfSaleController::class, 'store'])->name('trn.pos.store');
    Route::post('/point-of-sales/cashier/store', [PointOfSaleController::class, 'openCashier'])->name('trn.pos.open_store');
    Route::get('/point-of-sales/cashier/close', [PointOfSaleController::class, 'edit'])->name('trn.pos.close');
    Route::post('/point-of-sales/cashier/destroy', [PointOfSaleController::class, 'closeCashier'])->name('trn.pos.close_store');
    Route::get('/point-of-sales/payment/landing', [PointOfSaleController::class, 'paymentReturn'])->name('trn.pos.payment.return');
});
Route::get('/invoice/generate', [PointOfSaleController::class, 'generateInvoice'])->name('trn.pos.generate.invoice')->middleware('auth', 'verified');

Route::post('/point-of-sales/payment/callback', [PointOfSaleController::class, 'handleCallback'])->name('trn.pos.payment.callback')->withoutMiddleware([VerifyCsrfToken::class]);

// Route API dengan prefix "api"
Route::prefix('api')->group(function () {
    Route::middleware('auth')->post('/products/code/generate', [ProductController::class, 'generateCode'])->name('api.products.generateCode');
    Route::middleware('auth')->get('/warehouses', [WarehouseController::class, 'getWarehouses'])->name('api.warehouses');
    Route::middleware('auth')->get('/products', [ProductController::class, 'getProducts'])->name('api.products');
    Route::middleware('auth')->get('/product', [ProductController::class, 'getProduct'])->name('api.product');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
