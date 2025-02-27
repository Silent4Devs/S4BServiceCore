<?php

use Illuminate\Support\Facades\Route;
use Modules\Stripe\App\Http\Api\Controllers\Payment\S4BStripePaymentMetodController;
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
            Route::post('product',                          [S4BStripeProductMetodController::class, 'S4BGetProductMethod']);
            Route::post('allContractedProducts',            [S4BStripeProductMetodController::class, 'S4BPostProductsByCustomer']);
            Route::post('inactiveSubscriptionsByCustomer',  [S4BStripeProductMetodController::class, 'S4BPostInactiveSubscriptionsByCustomer']);
            Route::post('suscriptionByCustomer',            [S4BStripeProductMetodController::class, 'S4BPostProductsActiveInactive']);
            Route::get('productAll',                        [S4BStripeProductMetodController::class, 'S4BGetAllActiveProducts']);
            Route::get('unpurchasedProducts',               [S4BStripeProductMetodController::class, 'S4BGetUnpurchasedProducts']);
        });

        /*
            metodos de pago
        */
        Route::prefix('payment')->group(function () {
            Route::post('paymentMethod',                        [S4BStripePaymentMetodController::class, 'S4BPostPaymentMethod']);
            Route::post('addPaymentMethod',                     [S4BStripePaymentMetodController::class, 'S4BAddPaymentMethod']);
            Route::post('addCardPaymentMethod',                 [S4BStripePaymentMetodController::class, 'S4BAddCardPaymentMethod']);
        });

        /*
            historial
        */
        Route::prefix('history')->group(function () {});
    });
});
