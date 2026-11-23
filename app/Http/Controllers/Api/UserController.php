<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::withCount('media')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pengguna FlipPlay berhasil dimuat.',
            'count' => $users->count(),
            'data' => UserResource::collection($users),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'membership_type' => ['nullable', 'string', 'in:Regular,Premium,Platinum VIP'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email pengguna wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $validated['membership_type'] = $validated['membership_type'] ?? 'Regular';
        $validated['password'] = bcrypt($validated['password'] ?? 'password123');

        $user = User::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => "Pengguna {$user->name} berhasil ditambahkan.",
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        $user->load('media');
        $user->loadCount('media');

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pengguna berhasil dimuat.',
            'data' => new UserResource($user),
        ], 200);
    }
}
