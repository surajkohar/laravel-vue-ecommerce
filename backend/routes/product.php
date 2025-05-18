
<?php

use App\Http\Controllers\Admin\Products\ProductsController;

Route::post('/product/add', [ProductsController::class, 'create']);
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/product/{id}/edit', [ProductsController::class, 'edit']);
Route::put('/product/{id}/update', [ProductsController::class, 'update']);
Route::delete('/product/{id}', [ProductsController::class, 'delete']);
