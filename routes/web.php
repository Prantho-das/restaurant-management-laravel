<?php

use App\Http\Controllers\Api\OfflineSyncController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\Menu;
use App\Livewire\KdsBoard;
use App\Livewire\TableMenu;
use App\Models\KotOrder;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/optimize-clear', function () {
    Artisan::call('storage:link');

    Artisan::call('optimize:clear');

    return 'Application optimized and cache cleared!';
});
// --- Frontend Routes ---
Route::get('/', Home::class)->name('home');
Route::view('/menu', 'menu')->name('menu');
Route::view('/reservation', 'reservation')->name('reservation');
Route::view('/order', 'order')->name('order');

// QR Menu route points to Livewire component
Route::get('/table/{table:slug}', TableMenu::class)->name('table.menu');

// Standalone Full-Screen KDS
Route::middleware(['auth'])->get('/kds', KdsBoard::class)->name('kds.index');

// Note: Reports and other admin features
Route::middleware(['auth'])->prefix('admin/reports')->group(function () {
    Route::get('/sales-summary', [ReportController::class, 'salesSummary'])->name('reports.sales-summary');
    Route::get('/product-performance', [ReportController::class, 'productPerformance'])->name('reports.product-performance');
    Route::get('/inventory-wastage', [ReportController::class, 'inventoryWastage'])->name('reports.inventory-wastage');
    Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('/staff-performance', [ReportController::class, 'staffPerformance'])->name('reports.staff-performance');
    Route::get('/purchases', [ReportController::class, 'purchasesReport'])->name('reports.purchases');
    Route::get('/expenses', [ReportController::class, 'expensesReport'])->name('reports.expenses');
    Route::get('/stock-adjustments', [ReportController::class, 'stockAdjustmentsReport'])->name('reports.stock-adjustments');
    Route::get('/wastage', [ReportController::class, 'wastageReport'])->name('reports.wastage');
    Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('/category', [ReportController::class, 'categoryReport'])->name('reports.category');
    Route::get('/waiter', [ReportController::class, 'waiterReport'])->name('reports.waiter');
    Route::get('/due', [ReportController::class, 'dueReport'])->name('reports.due');
    Route::get('/discount', [ReportController::class, 'discountReport'])->name('reports.discount');
    Route::get('/channel', [ReportController::class, 'channelReport'])->name('reports.channel');
});

// Offline Sync API Routes
Route::middleware('auth')->prefix('api/offline')->name('api.offline.')->group(function () {
    Route::post('/queue', [OfflineSyncController::class, 'queueOrder'])->name('queue');
    Route::get('/pending', [OfflineSyncController::class, 'pendingCount'])->name('pending');
    Route::post('/sync', [OfflineSyncController::class, 'processQueue'])->name('sync');
    Route::get('/status', [OfflineSyncController::class, 'status'])->name('status');
});

// KOT Print Route
Route::middleware('auth')->get('/print/kot/{kotId}', function ($kotId) {
    $kot = KotOrder::with('items', 'order', 'sentBy')->findOrFail($kotId);

    return view('print.kot', [
        'kotNumber' => $kot->kot_number,
        'orderNumber' => $kot->order?->order_number,
        'date' => $kot->created_at->format('d/m/Y'),
        'time' => $kot->created_at->format('h:i A'),
        'orderType' => ucwords(str_replace('_', ' ', $kot->order?->order_type ?? 'takeaway')),
        'tableNumber' => $kot->order?->table_number,
        'items' => $kot->items->map(function ($item) {
            return [
                'item_name' => $item->item_name,
                'quantity' => $item->quantity,
                'notes' => $item->notes,
            ];
        }),
        'notes' => $kot->order?->notes,
        'sentBy' => $kot->sentBy?->name ?? 'System',
        'restaurantName' => Setting::getValue('site_name', config('app.name')),
        'restaurantAddress' => Setting::getValue('footer_address', ''),
        'restaurantPhone' => Setting::getValue('footer_phone', ''),
    ]);
})->name('print.kot');

// Payment Gateway Routes
Route::prefix('payment')->name('payment.')->group(function () {
    // POS/Admin Initiators (require auth)
    Route::middleware('auth')->group(function () {
        Route::get('bkash/initiate/{order}', [PaymentController::class, 'initiateBkash'])->name('bkash.initiate');
        Route::get('sslcommerze/initiate/{order}', [PaymentController::class, 'initiateSslcommerze'])->name('sslcommerze.initiate');
    });

    // Public Callbacks
    Route::get('bkash/callback', [PaymentController::class, 'bkashCallback'])->name('bkash.callback');
    Route::get('bkash/execute', [PaymentController::class, 'bkashExecute'])->name('bkash.execute');

    Route::any('sslcommerze/success', [PaymentController::class, 'sslcommerzeSuccess'])->name('sslcommerze.success');
    Route::any('sslcommerze/fail', [PaymentController::class, 'sslcommerzeFail'])->name('sslcommerze.fail');

    // SSLCommerze IPN - public endpoint for server-to-server notifications
    Route::post('sslcommerze/ipn', [PaymentController::class, 'sslcommerzeIpn'])->name('sslcommerze.ipn');
});

// Custom Pages (Catch-all)
Route::get('/{slug}', function ($slug) {
    $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

    return view('pages.show', compact('page'));
})->name('page.show');
