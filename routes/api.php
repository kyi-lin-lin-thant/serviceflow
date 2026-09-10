<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ServiceCategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Service Categories
Route::get('/service-categories', [ServiceCategoryController::class, 'index']);
Route::post('/service-categories', [ServiceCategoryController::class, 'store']);
Route::get('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'show']);
Route::put('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'update']);
// Route::patch('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'update']);
Route::delete('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'destroy']);

// Services
Route::get('/services', [ServiceController::class, 'index']);
Route::post('/services', [ServiceController::class, 'store']);
Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::put('/services/{service}', [ServiceController::class, 'update']);
// Route::patch('/services/{service}', [ServiceController::class, 'update']);
Route::delete('/services/{service}', [ServiceController::class, 'destroy']);