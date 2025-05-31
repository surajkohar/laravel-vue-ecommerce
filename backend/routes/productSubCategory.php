<?php

use App\Http\Controllers\Admin\Products\ProductSubCategoriesController;

Route::get('/product-subcategories', [ProductSubCategoriesController::class, 'index']);
Route::post('/product-subcategories/add', [ProductSubCategoriesController::class, 'add']);
Route::get('/product-subcategories/{id}/edit', [ProductSubCategoriesController::class, 'edit']);
Route::put('/product-subcategories/{id}/update', [ProductSubCategoriesController::class, 'edit']);
Route::delete('/product-subcategories/{id}/delete', [ProductSubCategoriesController::class, 'delete']);

Route::post('/product-subcategories/bulk-action/{action}', [ProductSubCategoriesController::class, 'bulkActions'])
->name('product-subcategories.bulk-action');
