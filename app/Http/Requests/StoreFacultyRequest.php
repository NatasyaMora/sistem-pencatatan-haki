<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:255', 'unique:faculties,nama'],
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
            'nama.required' => 'Nama Fakultas wajib diisi.',
            'nama.string' => 'Nama Fakultas harus berupa teks.',
            'nama.max' => 'Nama Fakultas tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama Fakultas sudah ada dalam database.',
        ];
    }
}
