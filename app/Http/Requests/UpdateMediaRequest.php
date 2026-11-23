<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $media = $this->route('media');
        $mediaId = $media instanceof Media ? $media->id : $media;
        $maxYear = (int) date('Y') + 5;

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'type' => ['sometimes', 'required', 'string', 'max:50'],
            'genre' => ['sometimes', 'required', 'string', 'max:100'],
            'release_year' => ['sometimes', 'required', 'integer', 'min:1888', "max:{$maxYear}"],
            'duration' => ['sometimes', 'required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah digunakan oleh media lain di FlipPlay.',
            'type.required' => 'Jenis media wajib diisi.',
            'genre.required' => 'Genre media wajib diisi.',
            'release_year.integer' => 'Tahun rilis harus berupa angka tahun valid.',
            'duration.integer' => 'Durasi media harus berupa angka (dalam menit).',
            'duration.min' => 'Durasi media minimal 1 menit.',
        ];
    }
}
