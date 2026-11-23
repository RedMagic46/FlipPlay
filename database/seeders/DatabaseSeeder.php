<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Pengguna (Sesuai skenario Modul 3: User Regular & Premium)
        $naufal = User::firstOrCreate(
            ['email' => 'naufal@flipplay.test'],
            [
                'name' => 'Naufal',
                'password' => Hash::make('password123'),
                'membership_type' => 'Regular',
            ]
        );

        $andi = User::firstOrCreate(
            ['email' => 'andi@flipplay.test'],
            [
                'name' => 'Andi',
                'password' => Hash::make('password123'),
                'membership_type' => 'Platinum VIP',
            ]
        );

        // 2. Seed Media (Sesuai katalog Modul 3)
        $media1 = Media::firstOrCreate(
            ['title' => 'Interstellar'],
            [
                'type' => 'Movie',
                'genre' => 'Sci-Fi',
                'release_year' => 2014,
                'duration' => 169,
                'description' => 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.',
            ]
        );

        $media2 = Media::firstOrCreate(
            ['title' => 'Inception'],
            [
                'type' => 'Movie',
                'genre' => 'Sci-Fi',
                'release_year' => 2010,
                'duration' => 148,
                'description' => 'A thief who steals corporate secrets through dream-sharing technology is given the inverse task of planting an idea.',
            ]
        );

        $media3 = Media::firstOrCreate(
            ['title' => 'The Dark Knight'],
            [
                'type' => 'Movie',
                'genre' => 'Action',
                'release_year' => 2008,
                'duration' => 152,
                'description' => 'When the menace known as the Joker wreaks havoc and chaos on Gotham, Batman must accept one of the greatest psychological tests.',
            ]
        );

        $media4 = Media::firstOrCreate(
            ['title' => 'Oppenheimer'],
            [
                'type' => 'Movie',
                'genre' => 'Biography',
                'release_year' => 2023,
                'duration' => 180,
                'description' => 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.',
            ]
        );

        $media5 = Media::firstOrCreate(
            ['title' => 'Spirited Away'],
            [
                'type' => 'Anime',
                'genre' => 'Animation',
                'release_year' => 2001,
                'duration' => 125,
                'description' => 'During her family\'s move to the suburbs, a sullen 10-year-old girl wanders into a world ruled by gods, witches and spirits.',
            ]
        );

        // 3. Seed Koleksi Pengguna (Koleksi Modul 3)
        // Naufal: Interstellar, Inception, Spirited Away
        $naufal->media()->syncWithoutDetaching([
            $media1->id => ['notes' => 'Masterpiece sci-fi favorit'],
            $media2->id => ['notes' => 'Konsep mimpi berlapis yang brilian'],
            $media5->id => ['notes' => 'Animasi Studio Ghibli terbaik sepanjang masa'],
        ]);

        // Andi: Interstellar, The Dark Knight, Oppenheimer
        $andi->media()->syncWithoutDetaching([
            $media1->id => ['notes' => 'Koleksi tontonan 4K'],
            $media3->id => ['notes' => 'Trilogi Batman Nolan nomor 1'],
            $media4->id => ['notes' => 'Film biografi sinematik'],
        ]);
    }
}
