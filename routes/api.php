<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AudioController;
use App\Http\Controllers\Api\BalanceController;
use App\Http\Controllers\Api\CallController;
use App\Http\Controllers\Api\CallerController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\ResellerPortalController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\TTS\ArtistController;
use App\Http\Controllers\Api\TTS\LanguageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index']);
        Route::put('/', [AccountController::class, 'update']);
        Route::put('/password', [AccountController::class, 'changePassword']);

        Route::get('/balance', [BalanceController::class, 'show']);
    });

    Route::apiResource('audio', AudioController::class);
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('templates', TemplateController::class);
    Route::apiResource('callers', CallerController::class)->only('index');
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('calls', CallController::class)->only(['index', 'store']);

    Route::apiResource('otp', OtpController::class)->only('index', 'store');

    Route::post('deposits/{deposit}/verify', [DepositController::class, 'verify'])->name('deposits.verify');
    Route::apiResource('deposits', DepositController::class)->only(['index', 'store', 'show']);

    Route::prefix('reseller')->group(function () {
        Route::get('branding', [ResellerPortalController::class, 'branding']);
        Route::put('branding', [ResellerPortalController::class, 'updateBranding']);
        Route::get('clients', [ResellerPortalController::class, 'clients']);
        Route::post('clients', [ResellerPortalController::class, 'createClient']);
    });

    Route::group(['prefix' => 'tts'], function () {
        Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);
        Route::apiResource('artists', ArtistController::class)->only(['index', 'show']);
    });
});
