<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCollectionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test melihat daftar koleksi pengguna (Happy Path - 200 OK)
     */
    public function test_can_get_user_collection_list(): void
    {
        $user = User::where('name', 'Naufal')->firstOrFail();

        $response = $this->getJson("/api/users/{$user->id}/collections");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'id',
                    'name',
                    'membership_type',
                ],
                'total_items',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'genre',
                        'release_year',
                        'duration',
                        'collection_meta' => [
                            'notes',
                            'added_at',
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => 'Naufal',
                ],
            ]);
    }

    /**
     * Custom Action: Menambahkan media baru ke koleksi pengguna (Happy Path - 201 Created)
     */
    public function test_can_add_media_to_user_collection_happy_path(): void
    {
        $user = User::where('name', 'Naufal')->firstOrFail();
        // Oppenheimer belum ada di koleksi Naufal (hanya ada di Andi)
        $media = Media::where('title', 'Oppenheimer')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collections", [
            'media_id' => $media->id,
            'notes' => 'Menambahkan film Oppenheimer ke antrean tontonan',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $media->id,
                    'title' => 'Oppenheimer',
                    'collection_meta' => [
                        'notes' => 'Menambahkan film Oppenheimer ke antrean tontonan',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('media_user', [
            'user_id' => $user->id,
            'media_id' => $media->id,
            'notes' => 'Menambahkan film Oppenheimer ke antrean tontonan',
        ]);
    }

    /**
     * Custom Action: Uji Skenario Gagal - Menambahkan media yang sama dua kali (Failure Path - 422)
     */
    public function test_cannot_add_same_media_twice_to_user_collection_failure_path(): void
    {
        $user = User::where('name', 'Naufal')->firstOrFail();
        // Interstellar sudah ada di koleksi Naufal
        $media = Media::where('title', 'Interstellar')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collections", [
            'media_id' => $media->id,
            'notes' => 'Mencoba menambahkan lagi',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'errors' => [
                    'media_id' => [
                        "Media 'Interstellar' sudah ada di dalam koleksi pengguna.",
                    ],
                ],
            ]);
    }

    /**
     * Custom Action: Validasi media_id tidak valid/tidak ada di database (Failure Path - 422)
     */
    public function test_cannot_add_non_existent_media_to_user_collection_failure_path(): void
    {
        $user = User::where('name', 'Naufal')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collections", [
            'media_id' => 99999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['media_id'])
            ->assertJsonFragment([
                'media_id' => ['Media dengan ID yang dipilih tidak terdaftar di FlipPlay.'],
            ]);
    }

    /**
     * Custom Action: Menghapus media dari koleksi pengguna (Happy Path - 200 OK)
     */
    public function test_can_remove_media_from_user_collection_happy_path(): void
    {
        $user = User::where('name', 'Naufal')->firstOrFail();
        // Inception ada di koleksi Naufal
        $media = Media::where('title', 'Inception')->firstOrFail();

        $response = $this->deleteJson("/api/users/{$user->id}/collections/{$media->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => "Media 'Inception' berhasil dihapus dari koleksi pengguna Naufal.",
            ]);

        $this->assertDatabaseMissing('media_user', [
            'user_id' => $user->id,
            'media_id' => $media->id,
        ]);
    }

    /**
     * Custom Action: Uji Skenario Gagal - Menghapus media yang tidak ada di koleksi (Failure Path - 404)
     */
    public function test_cannot_remove_media_not_in_user_collection_failure_path(): void
    {
        $user = User::where('name', 'Naufal')->firstOrFail();
        // Oppenheimer tidak ada di koleksi Naufal
        $media = Media::where('title', 'Oppenheimer')->firstOrFail();

        $response = $this->deleteJson("/api/users/{$user->id}/collections/{$media->id}");

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => "Media 'Oppenheimer' tidak ditemukan di dalam koleksi pengguna Naufal.",
            ]);
    }
}
