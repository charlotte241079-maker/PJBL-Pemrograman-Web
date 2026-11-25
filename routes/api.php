<?php

use App\Http\Controllers\Api\CurrencyController; // <--- VALIDASI
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('ingredients', IngredientController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('expenses', ExpenseController::class);

    Route::get('currency', [CurrencyController::class, 'index']); // <--- API MATA UANG
});