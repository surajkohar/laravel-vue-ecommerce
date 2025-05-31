<?php

use App\Http\Controllers\Admin\Products\ProductCategoriesController;

Route::get('/product-category', [ProductCategoriesController::class, 'index']);
Route::post('/product-category/add', [ProductCategoriesController::class, 'add']);
Route::get('/product-category/{id}/edit', [ProductCategoriesController::class, 'edit']);
Route::put('/product-category/{id}/update', [ProductCategoriesController::class, 'edit']);
Route::delete('/product-category/{id}/delete', [ProductCategoriesController::class, 'delete']);

