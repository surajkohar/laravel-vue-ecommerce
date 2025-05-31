<?php

use App\Http\Controllers\Admin\Products\SizeController;

Route::get('/product-sizes', [SizeController::class, 'index']);
Route::post('/product-sizes/save-all', [SizeController::class, 'add']);
Route::delete('/product-size/{id}/delete', [SizeController::class, 'delete']);

