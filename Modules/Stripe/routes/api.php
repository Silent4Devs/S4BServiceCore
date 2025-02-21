<?php

use Illuminate\Support\Facades\Route;
use Modules\Stripe\App\Http\Api\Controllers\Product\S4BStripeProductMetodController;

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

Route::middleware(['stripe.key'])->group(function () {
    Route::prefix('stripe')->group(function () {
        /*
            productos
        */
        Route::prefix('products')->group(function () {
            Route::post('getProduct', [S4BStripeProductMetodController::class, 'S4BGetProductMethod']);
            Route::post('getProductAll', [S4BStripeProductMetodController::class, 'S4BGetAllActiveProducts']);
        });

        /*
            metodos de pago
        */
        Route::prefix('payment')->group(function () {
            Route::post('getProduct', [S4BStripeProductMetodController::class, 'S4BGetProductMethod']);
            Route::post('getProductAll', [S4BStripeProductMetodController::class, 'S4BGetAllActiveProducts']);
        });

        /*
            historial
        */
        Route::prefix('history')->group(function () {
            Route::post('getProduct', [S4BStripeProductMetodController::class, 'S4BGetProductMethod']);
            Route::post('getProductAll', [S4BStripeProductMetodController::class, 'S4BGetAllActiveProducts']);
        });
    });
});
