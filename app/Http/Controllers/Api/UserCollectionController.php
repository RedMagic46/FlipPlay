<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserCollectionRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserCollectionController extends Controller
{
    /**
     * Menampilkan koleksi media milik seorang pengguna (Index Koleksi Pengguna)
     */
    public function index(User $user): JsonResponse
    {
        $collections = $user->media()
            ->withPivot('id', 'notes', 'created_at')
            ->orderByPivot('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => "Daftar koleksi media pengguna {$user->name} berhasil dimuat.",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'membership_type' => $user->membership_type,
            ],
            'total_items' => $collections->count(),
            'data' => MediaResource::collection($collections),
        ], 200);
    }

    /**
     * Custom Action: Menambahkan media ke dalam koleksi pengguna (Attach relasi many-to-many)
     * Termasuk validasi duplikasi koleksi.
     */
    public function store(StoreUserCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = (int) $request->validated('media_id');
        $notes = $request->validated('notes');

        $media = Media::findOrFail($mediaId);

        // Validasi duplikasi: Cek apakah media sudah ada dalam koleksi pengguna
        if ($user->media()->where('media_id', $mediaId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => "Media '{$media->title}' sudah ada di dalam koleksi {$user->name}.",
                'errors' => [
                    'media_id' => [
                        "Media '{$media->title}' sudah ada di dalam koleksi pengguna.",
                    ],
                ],
            ], 422);
        }

        // Attach relasi many-to-many beserta data pivot
        $user->media()->attach($mediaId, [
            'notes' => $notes,
        ]);

        $attachedMedia = $user->media()
            ->withPivot('id', 'notes', 'created_at')
            ->where('media_id', $mediaId)
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => "Media '{$media->title}' berhasil ditambahkan ke koleksi {$user->name}.",
            'data' => new MediaResource($attachedMedia),
        ], 201);
    }

    /**
     * Custom Action: Menghapus media dari koleksi pengguna (Detach relasi many-to-many)
     */
    public function destroy(User $user, Media $media): JsonResponse
    {
        // Cek apakah media memang ada di dalam koleksi pengguna
        if (! $user->media()->where('media_id', $media->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => "Media '{$media->title}' tidak ditemukan di dalam koleksi pengguna {$user->name}.",
            ], 404);
        }

        // Detach relasi
        $user->media()->detach($media->id);

        return response()->json([
            'status' => 'success',
            'message' => "Media '{$media->title}' berhasil dihapus dari koleksi pengguna {$user->name}.",
        ], 200);
    }
}
