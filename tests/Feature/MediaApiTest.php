<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed initial data
        $this->seed();
    }

    /**
     * Test mendapatkan daftar seluruh media (Happy Path)
     */
    public function test_can_get_all_media_list(): void
    {
        $response = $this->getJson('/api/media');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'count',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'type',
                        'genre',
                        'release_year',
                        'duration',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Test mencari media berdasarkan keyword pencarian (Query filter)
     */
    public function test_can_filter_media_by_search_keyword(): void
    {
        $response = $this->getJson('/api/media?search=Inception');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Inception',
            ]);
    }

    /**
     * Test filter media berdasarkan genre
     */
    public function test_can_filter_media_by_genre(): void
    {
        $response = $this->getJson('/api/media?genre=Sci-Fi');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        foreach ($data as $item) {
            $this->assertEquals('Sci-Fi', $item['genre']);
        }
    }

    /**
     * Test menambahkan media baru (Happy Path - 201 Created)
     */
    public function test_can_create_new_media_happy_path(): void
    {
        $payload = [
            'title' => 'Dune: Part Two',
            'type' => 'Movie',
            'genre' => 'Sci-Fi',
            'release_year' => 2024,
            'duration' => 166,
            'description' => 'Paul Atreides unites with Chani and the Fremen while seeking revenge.',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'title' => 'Dune: Part Two',
                    'type' => 'Movie',
                    'genre' => 'Sci-Fi',
                    'release_year' => 2024,
                    'duration' => 166,
                ],
            ]);

        $this->assertDatabaseHas('media', [
            'title' => 'Dune: Part Two',
        ]);
    }

    /**
     * Test validasi judul kosong (Failure Path - 422 Unprocessable Entity)
     */
    public function test_cannot_create_media_with_empty_title_failure_path(): void
    {
        $payload = [
            'title' => '',
            'type' => 'Movie',
            'genre' => 'Action',
            'release_year' => 2024,
            'duration' => 120,
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJsonFragment([
                'title' => ['Judul media wajib diisi.'],
            ]);
    }

    /**
     * Test validasi judul duplikat (Failure Path - 422 Unprocessable Entity)
     */
    public function test_cannot_create_media_with_duplicate_title_failure_path(): void
    {
        $payload = [
            'title' => 'Interstellar', // Sudah ada di seeder
            'type' => 'Movie',
            'genre' => 'Sci-Fi',
            'release_year' => 2014,
            'duration' => 169,
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJsonFragment([
                'title' => ['Judul media sudah terdaftar di sistem FlipPlay.'],
            ]);
    }

    /**
     * Test menampilkan detail satu media beserta relasi users yang mengoleksi (Happy Path)
     */
    public function test_can_show_media_detail_with_users_relation(): void
    {
        $media = Media::where('title', 'Interstellar')->firstOrFail();

        $response = $this->getJson("/api/media/{$media->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'users' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'membership_type',
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $media->id,
                    'title' => 'Interstellar',
                ],
            ]);

        // Cek bahwa relasi users ter-load
        $users = $response->json('data.users');
        $this->assertIsArray($users);
        $this->assertNotEmpty($users);
    }

    /**
     * Test menampilkan media yang tidak ada (Failure Path - 404 Not Found)
     */
    public function test_show_non_existent_media_returns_404(): void
    {
        $response = $this->getJson('/api/media/99999');

        $response->assertStatus(404);
    }

    /**
     * Test memperbarui media (Happy Path - 200 OK)
     */
    public function test_can_update_media_happy_path(): void
    {
        $media = Media::where('title', 'Spirited Away')->firstOrFail();

        $updatePayload = [
            'title' => 'Spirited Away (Remastered 4K)',
            'duration' => 126,
        ];

        $response = $this->patchJson("/api/media/{$media->id}", $updatePayload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $media->id,
                    'title' => 'Spirited Away (Remastered 4K)',
                    'duration' => 126,
                ],
            ]);

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'title' => 'Spirited Away (Remastered 4K)',
        ]);
    }

    /**
     * Test memperbarui media dengan judul media lain (Failure Path - 422)
     */
    public function test_cannot_update_media_with_duplicate_title_failure_path(): void
    {
        $media = Media::where('title', 'Spirited Away')->firstOrFail();

        $response = $this->patchJson("/api/media/{$media->id}", [
            'title' => 'Interstellar', // Judul milik media lain
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test menghapus media (Happy Path - 200 OK)
     */
    public function test_can_delete_media_happy_path(): void
    {
        $media = Media::create([
            'title' => 'Temporary Media to Delete',
            'type' => 'Movie',
            'genre' => 'Comedy',
            'release_year' => 2022,
            'duration' => 90,
        ]);

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseMissing('media', [
            'id' => $media->id,
        ]);
    }
}
