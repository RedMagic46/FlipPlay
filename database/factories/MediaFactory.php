<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'type' => fake()->randomElement(['Movie', 'Series', 'Anime', 'Documentary']),
            'genre' => fake()->randomElement(['Sci-Fi', 'Action', 'Biography', 'Animation', 'Drama', 'Comedy']),
            'release_year' => fake()->numberBetween(1995, 2026),
            'duration' => fake()->numberBetween(45, 210),
            'description' => fake()->paragraph(),
        ];
    }
}
