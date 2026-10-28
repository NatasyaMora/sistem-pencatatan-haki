<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisHakiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', 'unique:jenis_hakis,nama'],
        ];
    }

    /**
     * Custom error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama Jenis HAKI wajib diisi.',
            'nama.string' => 'Nama Jenis HAKI harus berupa teks.',
            'nama.max' => 'Nama Jenis HAKI tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama Jenis HAKI sudah ada dalam database.',
        ];
    }
}
