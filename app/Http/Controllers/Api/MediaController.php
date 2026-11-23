<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'title', 'genre', 'type']);

        $media = Media::filter($filters)
            ->withCount('users')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar media FlipPlay berhasil dimuat.',
            'count' => $media->count(),
            'data' => MediaResource::collection($media),
        ], 200);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => "Media '{$media->title}' berhasil ditambahkan ke katalog FlipPlay.",
            'data' => new MediaResource($media),
        ], 201);
    }

    public function show(Media $media): JsonResponse
    {
        $media->load('users');
        $media->loadCount('users');

        return response()->json([
            'status' => 'success',
            'message' => 'Detail media berhasil dimuat.',
            'data' => new MediaResource($media),
        ], 200);
    }

    public function update(UpdateMediaRequest $request, Media $media): JsonResponse
    {
        $media->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => "Data media '{$media->title}' berhasil diperbarui.",
            'data' => new MediaResource($media),
        ], 200);
    }

    public function destroy(Media $media): JsonResponse
    {
        $title = $media->title;
        $media->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Media '{$title}' berhasil dihapus dari sistem FlipPlay.",
        ], 200);
    }
}
