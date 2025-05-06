<?php

//use App\Modules\Stripe\Controllers\S4BStripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Route::post('/stripe/charge', [S4BStripeController::class, 'charge']);
Route::middleware(['stripe.key'])->group(function () {});
