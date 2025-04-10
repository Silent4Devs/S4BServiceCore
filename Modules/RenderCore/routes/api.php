<?php

use Illuminate\Support\Facades\Route;
use Modules\RenderCore\App\Http\Api\Controllers\ExcelExportController;
use Modules\RenderCore\Http\Controllers\RenderCoreController;

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

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rendercore', RenderCoreController::class)->names('rendercore');
});

Route::prefix('render')->group(function () {
    /*
        excel
    */
    Route::prefix('excel')->group(function () {
        Route::post('excelExport',                          [ExcelExportController::class, 'export']);
    });
});
