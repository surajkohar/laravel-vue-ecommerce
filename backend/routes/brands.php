<?php

use App\Http\Controllers\Admin\Products\BrandsController;

Route::get('/brands', [BrandsController::class, 'index']);
Route::post('/brands/save-all', [BrandsController::class, 'add']);
Route::delete('/brands/{id}/delete', [BrandsController::class, 'delete']);

