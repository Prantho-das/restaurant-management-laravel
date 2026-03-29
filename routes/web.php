<?php

use App\Http\Controllers\Api\OfflineSyncController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Livewire\TableMenu;
use App\Models\Setting;
use App\Services\MetaService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $settings = Setting::where('group', 'landing_page')->pluck('value', 'key');

    // Convert lp_ prefixed keys to match view variable expectations
    $cms = (object) $settings->mapWithKeys(function ($value, $key) {
        return [str_replace('lp_', '', $key) => $value];
    })->all();

    // Server-side Meta CAPI Tracking
    app(MetaService::class)->sendEvent('PageView');

    return view('welcome', compact('cms'));
})->name('home');

Route::view('/menu', 'menu')->name('menu');
Route::view('/reservation', 'reservation')->name('reservation');
Route::view('/order', 'order')->name('order');

// QR Menu route points to Livewire component
Route::get('/table/{table:slug}', TableMenu::class)->name('table.menu');

// Note: Reports and other admin features
Route::middleware(['auth'])->prefix('admin/reports')->group(function () {
    Route::get('/sales-summary', [ReportController::class, 'salesSummary'])->name('reports.sales-summary');
    Route::get('/product-performance', [ReportController::class, 'productPerformance'])->name('reports.product-performance');
    Route::get('/inventory-wastage', [ReportController::class, 'inventoryWastage'])->name('reports.inventory-wastage');
    Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('/staff-performance', [ReportController::class, 'staffPerformance'])->name('reports.staff-performance');
});

// Offline Sync API Routes
Route::middleware('auth')->prefix('api/offline')->name('api.offline.')->group(function () {
    Route::post('/queue', [OfflineSyncController::class, 'queueOrder'])->name('queue');
    Route::get('/pending', [OfflineSyncController::class, 'pendingCount'])->name('pending');
    Route::post('/sync', [OfflineSyncController::class, 'processQueue'])->name('sync');
    Route::get('/status', [OfflineSyncController::class, 'status'])->name('status');
});

// Payment Gateway Routes
Route::prefix('payment')->name('payment.')->group(function () {
    // bKash Routes (require auth)
    Route::middleware('auth')->group(function () {
        Route::get('bkash/initiate/{order}', [PaymentController::class, 'initiateBkash'])->name('bkash.initiate');
        Route::get('bkash/callback', [PaymentController::class, 'bkashCallback'])->name('bkash.callback');
        Route::get('bkash/execute', [PaymentController::class, 'bkashExecute'])->name('bkash.execute');

        Route::get('sslcommerze/initiate/{order}', [PaymentController::class, 'initiateSslcommerze'])->name('sslcommerze.initiate');
        Route::get('sslcommerze/success', [PaymentController::class, 'sslcommerzeSuccess'])->name('sslcommerze.success');
        Route::get('sslcommerze/fail', [PaymentController::class, 'sslcommerzeFail'])->name('sslcommerze.fail');
    });

    // SSLCommerze IPN - public endpoint for server-to-server notifications
    Route::post('sslcommerze/ipn', [PaymentController::class, 'sslcommerzeIpn'])->name('sslcommerze.ipn');
});
