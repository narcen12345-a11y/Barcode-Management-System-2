<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialModelController;
use App\Http\Controllers\MaterialTypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Authentication (Public)
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Users
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:read-user');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:read-user');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:create-user');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:update-user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-user');
    Route::post('/users/{user}/restore', [UserController::class, 'restore'])->middleware('permission:delete-user');
    Route::post('/users/{user}/verify', [UserController::class, 'verify'])->middleware('permission:verify-user');
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->middleware('permission:activate-user');
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->middleware('permission:deactivate-user');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware('permission:reset-password');

    // Roles (Super Admin only via CheckPermission middleware)
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:read-role');
    Route::get('/roles/all', [RoleController::class, 'all'])->middleware('permission:read-role');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:read-role');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:create-role');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:update-role');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-role');
    Route::post('/roles/{role}/restore', [RoleController::class, 'restore'])->middleware('permission:delete-role');

    // Permissions (Super Admin only via CheckPermission middleware)
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:read-permission');
    Route::get('/permissions/all', [PermissionController::class, 'all'])->middleware('permission:read-permission');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->middleware('permission:read-permission');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:create-permission');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:update-permission');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:delete-permission');
    Route::post('/permissions/{permission}/restore', [PermissionController::class, 'restore'])->middleware('permission:delete-permission');

    // Master Data - Sites
    Route::get('/sites', [SiteController::class, 'index'])->middleware('permission:read-site');
    Route::get('/sites/all', [SiteController::class, 'all'])->middleware('permission:read-site');
    Route::get('/sites/{site}', [SiteController::class, 'show'])->middleware('permission:read-site');
    Route::post('/sites', [SiteController::class, 'store'])->middleware('permission:create-site');
    Route::put('/sites/{site}', [SiteController::class, 'update'])->middleware('permission:update-site');
    Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->middleware('permission:delete-site');
    Route::post('/sites/{site}/restore', [SiteController::class, 'restore'])->middleware('permission:delete-site');

    // Master Data - Material Types
    Route::get('/material-types', [MaterialTypeController::class, 'index'])->middleware('permission:read-material-type');
    Route::get('/material-types/all', [MaterialTypeController::class, 'all'])->middleware('permission:read-material-type');
    Route::get('/material-types/{material_type}', [MaterialTypeController::class, 'show'])->middleware('permission:read-material-type');
    Route::post('/material-types', [MaterialTypeController::class, 'store'])->middleware('permission:create-material-type');
    Route::put('/material-types/{material_type}', [MaterialTypeController::class, 'update'])->middleware('permission:update-material-type');
    Route::delete('/material-types/{material_type}', [MaterialTypeController::class, 'destroy'])->middleware('permission:delete-material-type');
    Route::post('/material-types/{material_type}/restore', [MaterialTypeController::class, 'restore'])->middleware('permission:delete-material-type');

    // Master Data - Material Models
    Route::get('/material-models', [MaterialModelController::class, 'index'])->middleware('permission:read-material-model');
    Route::get('/material-models/all', [MaterialModelController::class, 'all'])->middleware('permission:read-material-model');
    Route::get('/material-models/by-material-type/{materialTypeId}', [MaterialModelController::class, 'byMaterialType'])->middleware('permission:read-material-model');
    Route::get('/material-models/{material_model}', [MaterialModelController::class, 'show'])->middleware('permission:read-material-model');
    Route::post('/material-models', [MaterialModelController::class, 'store'])->middleware('permission:create-material-model');
    Route::put('/material-models/{material_model}', [MaterialModelController::class, 'update'])->middleware('permission:update-material-model');
    Route::delete('/material-models/{material_model}', [MaterialModelController::class, 'destroy'])->middleware('permission:delete-material-model');
    Route::post('/material-models/{material_model}/restore', [MaterialModelController::class, 'restore'])->middleware('permission:delete-material-model');

    // Master Data - Materials
    Route::get('/materials', [MaterialController::class, 'index'])->middleware('permission:read-material');
    Route::get('/materials/all', [MaterialController::class, 'all'])->middleware('permission:read-material');
    Route::get('/materials/{material}', [MaterialController::class, 'show'])->middleware('permission:read-material');
    Route::post('/materials', [MaterialController::class, 'store'])->middleware('permission:create-material');
    Route::put('/materials/{material}', [MaterialController::class, 'update'])->middleware('permission:update-material');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->middleware('permission:delete-material');
    Route::post('/materials/{material}/restore', [MaterialController::class, 'restore'])->middleware('permission:delete-material');

    // Barcode
    Route::get('/barcodes', [BarcodeController::class, 'index'])->middleware('permission:read-barcode');
    Route::get('/barcodes/all', [BarcodeController::class, 'all'])->middleware('permission:read-barcode');
    Route::get('/barcodes/by-barcode-id/{barcodeId}', [BarcodeController::class, 'showByBarcodeId'])->middleware('permission:read-barcode');
    Route::get('/barcodes/{barcode}', [BarcodeController::class, 'show'])->middleware('permission:read-barcode');
    Route::post('/barcodes', [BarcodeController::class, 'store'])->middleware('permission:create-barcode');
    Route::put('/barcodes/{barcode}', [BarcodeController::class, 'update'])->middleware('permission:update-barcode');
    Route::delete('/barcodes/{barcode}', [BarcodeController::class, 'destroy'])->middleware('permission:delete-barcode');
    Route::post('/barcodes/{barcode}/restore', [BarcodeController::class, 'restore'])->middleware('permission:delete-barcode');
    Route::get('/barcodes/{barcode}/history', [BarcodeController::class, 'history'])->middleware('permission:read-barcode');
});
