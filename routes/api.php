<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserCollectionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class)->parameters([
    'media' => 'media',
]);

Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::post('users', [UserController::class, 'store'])->name('users.store');
Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

Route::get('users/{user}/collections', [UserCollectionController::class, 'index'])->name('users.collections.index');
Route::post('users/{user}/collections', [UserCollectionController::class, 'store'])->name('users.collections.store');
Route::delete('users/{user}/collections/{media}', [UserCollectionController::class, 'destroy'])->name('users.collections.destroy');
