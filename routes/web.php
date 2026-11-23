<?php

use Illuminate\Support\Facades\Route;

// Redirect akses browser dari root dan tanpa prefix /api langsung ke endpoint API yang sesuai
Route::get('/', function () {
    return response()->json([
        'name' => 'FlipPlay REST API - Laravel 12',
        'version' => '1.0.0',
        'status' => 'active',
        'documentation' => 'Akses endpoint API menggunakan prefix /api',
        'endpoints' => [
            'media' => url('/api/media'),
            'users' => url('/api/users'),
            'user_collections' => url('/api/users/1/collections'),
        ],
    ]);
});

Route::redirect('/media', '/api/media');
Route::redirect('/users', '/api/users');
