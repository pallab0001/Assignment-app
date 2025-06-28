<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserDataController;

Route::get('/', [UserDataController::class, 'index']);
Route::post('/store', [UserDataController::class, 'store']);
Route::get('/edit/{id}', [UserDataController::class, 'edit']);
Route::post('/update/{id}', [UserDataController::class, 'update']);
Route::get('/delete/{id}', [UserDataController::class, 'destroy']);
Route::get('/view-json', [UserDataController::class, 'viewJson']);
