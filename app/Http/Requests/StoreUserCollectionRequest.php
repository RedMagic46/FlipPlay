<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_id' => ['required', 'integer', 'exists:media,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'media_id.required' => 'ID media wajib disertakan untuk ditambahkan ke koleksi.',
            'media_id.integer' => 'ID media harus berupa bilangan bulat.',
            'media_id.exists' => 'Media dengan ID yang dipilih tidak terdaftar di FlipPlay.',
            'notes.max' => 'Catatan pribadi maksimal 500 karakter.',
        ];
    }
}
