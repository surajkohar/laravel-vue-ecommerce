<?php

use App\Http\Controllers\Admin\Products\ProductCategoriesController;
use App\Http\Controllers\Admin\SizeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UsersController;
use App\Models\Admin\Products;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/roles/create', [RoleController::class, 'create']);
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'edit']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'deleteRole']);
    Route::post('/permissions/create', [PermissionController::class, 'create']);
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{id}', [PermissionController::class, 'edit']);
    Route::put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionController::class, 'deletePermission']);
    Route::get('/rolesAndPermissions', [RoleController::class, 'fetchRolePermission']);

    Route::post('/users/{id}/assign-role', [RoleController::class, 'assignRole']);
    Route::post('/users/{id}/remove-role', [RoleController::class, 'removeRole']);
    Route::get('/users/{id}/roles', [RoleController::class, 'listRoles']);
    Route::post('/roles/{id}/assign-permission', [PermissionController::class, 'assignPermission']);
    Route::post('/roles/{id}/remove-permission', [PermissionController::class, 'removePermission']);
    Route::get('/roles/{id}/permissions', [PermissionController::class, 'listPermissions']);

    Route::post('/user/add', [UsersController::class, 'create']);
    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/user/{id}/edit', [UsersController::class, 'edit']);
    Route::put('/user/{id}/update', [UsersController::class, 'update']);

    Route::get('/sizes',[SizeController::class,'index']);
    Route::post('/sizes/add',[SizeController::class,'add']);


    require_once __DIR__ . '/product.php';
    require_once __DIR__ . '/productCategory.php';
    require_once __DIR__ . '/productSubCategory.php';
    require_once __DIR__ . '/sizes.php';


});
