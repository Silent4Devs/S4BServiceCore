<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth4You\App\Http\Api\S4BAuth4YouController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::get('prueba', [S4BAuth4YouController::class, 'prueba']);

Route::prefix('auth')->group(function () {
    Route::post('register',                 [S4BAuth4YouController::class, 'register']);
    Route::post('login',                    [S4BAuth4YouController::class, 'login']);
    Route::post('forgot-password',          [S4BAuth4YouController::class, 'forgotPassword']);
    Route::post('reset-password',           [S4BAuth4YouController::class, 'resetPassword']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout',                   [S4BAuth4YouController::class, 'logout']);
        Route::post('verify-2fa',               [S4BAuth4YouController::class, 'verify2FA']);
        Route::post('disable-2fa',              [S4BAuth4YouController::class, 'disable2FA']);
        Route::post('enable-2fa',               [S4BAuth4YouController::class, 'enable2FA']);
        Route::get('me', [S4BAuth4YouController::class, 'me']);
    });
});
