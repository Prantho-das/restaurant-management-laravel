<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $cms = \App\Models\LandingPage::where('is_active', true)->first();
    $signatureMenuItems = \App\Models\MenuItem::where('is_active', true)->take(4)->get();
    
    // Server-side Meta CAPI Tracking
    app(\App\Services\MetaService::class)->sendEvent('PageView');

    return view('welcome', compact('cms', 'signatureMenuItems'));
})->name('home');
Route::view('/menu', 'menu')->name('menu');
Route::view('/reservation', 'reservation')->name('reservation');
Route::view('/order', 'order')->name('order');
