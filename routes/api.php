<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserCollectionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FlipPlay REST API Routes
|--------------------------------------------------------------------------
|
| Seluruh endpoint API FlipPlay terdaftar di sini dan dapat diakses dengan
| prefix /api.
|
*/

// 1. Endpoint CRUD Media (index, store, show, update, destroy)
Route::apiResource('media', MediaController::class)->parameters([
    'media' => 'media',
]);

// 2. Endpoint Data Pengguna
Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::post('users', [UserController::class, 'store'])->name('users.store');
Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

// 3. Custom Action: Koleksi Media Pengguna (Relasi Many-to-Many Attach & Detach)
// - Menampilkan koleksi media milik seorang pengguna
Route::get('users/{user}/collections', [UserCollectionController::class, 'index'])->name('users.collections.index');
// - Menambahkan media ke dalam koleksi pribadi pengguna (Attach)
Route::post('users/{user}/collections', [UserCollectionController::class, 'store'])->name('users.collections.store');
// - Menghapus media dari koleksi pengguna (Detach)
Route::delete('users/{user}/collections/{media}', [UserCollectionController::class, 'destroy'])->name('users.collections.destroy');
