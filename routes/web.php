<?php

declare(strict_types=1);

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminPortalController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\ResellerPortalController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('auth/login', function () {
    return redirect('login');
})->name('login');

Route::view('/terms', 'terms')->name('terms');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/resellers', [AdminPortalController::class, 'resellers'])->name('resellers');
    Route::post('/resellers', [AdminPortalController::class, 'storeReseller'])->name('resellers.store');
    Route::get('/clients', [AdminPortalController::class, 'clients'])->name('clients');
    Route::get('/servers', [AdminPortalController::class, 'servers'])->name('servers');
    Route::get('/callers', [AdminPortalController::class, 'callers'])->name('callers');
    Route::get('/calls', [AdminPortalController::class, 'calls'])->name('calls');
    Route::get('/deposits', [AdminPortalController::class, 'deposits'])->name('deposits');
});

Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('/', [ClientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/calls', [ClientPortalController::class, 'calls'])->name('calls');
    Route::get('/campaigns', [ClientPortalController::class, 'campaigns'])->name('campaigns');
    Route::get('/callers', [ClientPortalController::class, 'callers'])->name('callers');
    Route::put('/callers/{caller}', [ClientPortalController::class, 'updateCaller'])->name('callers.update');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth')->name('logout');

Route::middleware(['auth'])->prefix('reseller')->name('reseller.')->group(function () {
    Route::get('/', [ResellerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/branding', [ResellerPortalController::class, 'branding'])->name('branding');
    Route::put('/branding', [ResellerPortalController::class, 'updateBranding'])->name('branding.update');
    Route::post('/clients', [ResellerPortalController::class, 'storeClient'])->name('clients.store');
});

Route::match(['post', 'get'], 'payments/{gateway}/callback/{deposit}', PaymentController::class)
    ->name('payments.callback')
    ->middleware(['auth'])
    ->whereIn('gateway', ['piprapay']);

Route::match(['post', 'get'], 'webhooks/payment/{gateway}', PaymentWebhookController::class)
    ->name('payments.webhook')
    ->whereIn('gateway', ['piprapay']);
