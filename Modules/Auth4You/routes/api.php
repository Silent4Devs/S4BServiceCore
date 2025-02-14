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
    Route::post('register', [S4BAuth4YouController::class, 'register']);
    Route::post('login', [S4BAuth4YouController::class, 'login']);
});
