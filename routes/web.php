<?php

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
