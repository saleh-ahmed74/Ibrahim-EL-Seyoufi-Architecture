<?php

use App\Http\Controllers\Api\PublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/projects', [PublicController::class, 'projects']);
Route::get('/projects/{slug}', [PublicController::class, 'project']);
Route::get('/categories', [PublicController::class, 'categories']);
Route::get('/site-settings', [PublicController::class, 'settings']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
