<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHakiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul_haki' => ['required', 'string', 'max:255'],
            'jenis_haki' => ['required', 'string', 'max:255'],
            'nomor_sertifikat' => ['required', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_kadaluarsa' => [
                'required',
                'date',
                'after_or_equal:tanggal_terbit',
            ],
            'tahun' => ['required', 'integer', 'min:1900', 'max:2100'],
            'deskripsi' => ['nullable', 'string'],
            'file_sertifikat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:3072'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul_haki.required' => 'Judul HAKI wajib diisi.',
            'judul_haki.max' => 'Judul HAKI maksimal 255 karakter.',
            'jenis_haki.required' => 'Jenis HAKI wajib dipilih.',
            'nomor_sertifikat.required' => 'Nomor sertifikat wajib diisi.',
            'tanggal_terbit.required' => 'Tanggal terbit wajib diisi.',
            'tanggal_terbit.date' => 'Format tanggal terbit tidak valid.',
            'tanggal_kadaluarsa.required' => 'Tanggal kedaluwarsa wajib diisi.',
            'tanggal_kadaluarsa.date' => 'Format tanggal kedaluwarsa tidak valid.',
            'tanggal_kadaluarsa.after_or_equal' => 'Tanggal kedaluwarsa harus sama dengan atau setelah tanggal terbit.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.min' => 'Tahun minimal 1900.',
            'tahun.max' => 'Tahun maksimal 2100.',
            'file_sertifikat.file' => 'File sertifikat harus berupa berkas.',
            'file_sertifikat.mimes' => 'Format file sertifikat harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_sertifikat.max' => 'Ukuran file sertifikat maksimal 3 MB.',
        ];
    }
}
