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
            Route::post('postProduct',                          [S4BStripeProductMetodController::class, 'S4BGetProductMethod']);
            Route::post('postAllContractedProducts',            [S4BStripeProductMetodController::class, 'S4BPostProductsByCustomer']);
            Route::post('postInactiveSubscriptionsByCustomer',  [S4BStripeProductMetodController::class, 'S4BPostInactiveSubscriptionsByCustomer']);
            Route::get('getProductAll',                         [S4BStripeProductMetodController::class, 'S4BGetAllActiveProducts']);
            Route::get('getUnpurchasedProducts',                [S4BStripeProductMetodController::class, 'S4BGetUnpurchasedProducts']);
        });

        /*
            metodos de pago
        */
        Route::prefix('payment')->group(function () {});

        /*
            historial
        */
        Route::prefix('history')->group(function () {});
    });
});
