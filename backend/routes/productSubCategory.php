<?php

use App\Http\Controllers\Admin\Products\ProductSubCategoriesController;

Route::get('/product-subcategories', [ProductSubCategoriesController::class, 'index']);
Route::post('/product-subcategories/add', [ProductSubCategoriesController::class, 'create']);
Route::get('/product-subcategories/{id}/edit', [ProductSubCategoriesController::class, 'edit']);
Route::put('/product-subcategories/{id}/update', [ProductSubCategoriesController::class, 'update']);
Route::delete('/product-subcategories/{id}', [ProductSubCategoriesController::class, 'delete']);

