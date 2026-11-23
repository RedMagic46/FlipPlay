<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxYear = (int) date('Y') + 5;

        return [
            'title' => ['required', 'string', 'max:255', 'unique:media,title'],
            'type' => ['required', 'string', 'max:50'],
            'genre' => ['required', 'string', 'max:100'],
            'release_year' => ['required', 'integer', 'min:1888', "max:{$maxYear}"],
            'duration' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah terdaftar di sistem FlipPlay.',
            'type.required' => 'Jenis media (Movie, Series, Anime, dll.) wajib diisi.',
            'genre.required' => 'Genre media wajib diisi.',
            'release_year.required' => 'Tahun rilis wajib diisi.',
            'release_year.integer' => 'Tahun rilis harus berupa angka tahun valid.',
            'release_year.min' => 'Tahun rilis tidak boleh lebih awal dari tahun 1888.',
            'duration.required' => 'Durasi media wajib diisi.',
            'duration.integer' => 'Durasi media harus berupa angka (dalam menit).',
            'duration.min' => 'Durasi media minimal 1 menit.',
        ];
    }
}
