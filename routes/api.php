<?php

use Illuminate\Support\Facades\Route;
use Just\Warehouse\Nova\Scan\Http\Controllers\InventoryController;
use Just\Warehouse\Nova\Scan\Http\Controllers\AddInventoryController;

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

Route::post('locations/{location}/add-inventory', [AddInventoryController::class, 'store']);

Route::get('locations/{location}/inventory', [InventoryController::class, 'show']);
