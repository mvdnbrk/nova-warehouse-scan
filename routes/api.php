<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Just\Warehouse\Nova\Scan\Http\Controllers\AddInventoryController;
use Just\Warehouse\Nova\Scan\Http\Controllers\InventoryController;
use Just\Warehouse\Nova\Scan\Http\Controllers\LocationController;
use Just\Warehouse\Nova\Scan\Http\Controllers\MoveInventoryController;
use Just\Warehouse\Nova\Scan\Http\Controllers\OrderFulfilledController;
use Just\Warehouse\Nova\Scan\Http\Controllers\OrderPickListController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('locations/{location}', [LocationController::class, 'show']);

Route::post('locations/{location}/add-inventory', [AddInventoryController::class, 'store']);

Route::get('locations/{location}/inventory', [InventoryController::class, 'show']);
Route::post('locations/{location}/move-inventory', [MoveInventoryController::class, 'store'])->withoutMiddleware([
    \App\Http\Middleware\VerifyCsrfToken::class,
    VerifyCsrfToken::class,
]);

Route::get('orders/open', [OrderPickListController::class, 'index']);
Route::get('orders/{order}/picklist', [OrderPickListController::class, 'show']);
Route::post('orders/{order}/fulfilled', [OrderFulfilledController::class, 'store'])->withoutMiddleware([
    \App\Http\Middleware\VerifyCsrfToken::class,
    VerifyCsrfToken::class,
]);
