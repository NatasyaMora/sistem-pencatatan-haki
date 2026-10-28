<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHakiRequest extends FormRequest
{
    /**
     * Menentukan apakah user boleh mencatat HAKI.
     */
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->isUser();
    }

    /**
     * Aturan validasi data HAKI.
     */
    public function rules(): array
    {
        return [
            'judul_haki' => [
                'required',
                'string',
                'max:255',
            ],

            'jenis_haki_id' => [
                'required',
                'integer',
                Rule::exists('jenis_hakis', 'id')
                    ->where('status', 'aktif'),
            ],

            'nomor_sertifikat' => [
                'required',
                'string',
                'max:255',
            ],

            'tanggal_terbit' => [
                'required',
                'date',
            ],

            'tanggal_kadaluarsa' => [
                'required',
                'date',
                'after_or_equal:tanggal_terbit',
            ],

            'faculty_id' => [
                'required',
                'integer',
                Rule::exists('faculties', 'id')
                    ->where('status', 'aktif'),
            ],

            'study_program_id' => [
                'required',
                'integer',
                Rule::exists('study_programs', 'id')
                    ->where('status', 'aktif')
                    ->where('faculty_id', $this->input('faculty_id')),
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],

            'file_sertifikat' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:3072',
            ],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'judul_haki.required' =>
                'Judul HAKI wajib diisi.',
            'judul_haki.max' =>
                'Judul HAKI maksimal 255 karakter.',

            'jenis_haki_id.required' =>
                'Jenis HAKI wajib dipilih.',
            'jenis_haki_id.integer' =>
                'Jenis HAKI yang dipilih tidak valid.',
            'jenis_haki_id.exists' =>
                'Jenis HAKI tidak tersedia atau sudah nonaktif.',

            'nomor_sertifikat.required' =>
                'Nomor sertifikat wajib diisi.',
            'nomor_sertifikat.max' =>
                'Nomor sertifikat maksimal 255 karakter.',

            'tanggal_terbit.required' =>
                'Tanggal terbit wajib diisi.',
            'tanggal_terbit.date' =>
                'Tanggal terbit tidak valid.',

            'tanggal_kadaluarsa.required' =>
                'Tanggal kadaluarsa wajib diisi.',
            'tanggal_kadaluarsa.date' =>
                'Tanggal kadaluarsa tidak valid.',
            'tanggal_kadaluarsa.after_or_equal' =>
                'Tanggal kadaluarsa harus sama dengan atau setelah tanggal terbit.',

            'faculty_id.required' =>
                'Fakultas wajib dipilih.',
            'faculty_id.integer' =>
                'Fakultas yang dipilih tidak valid.',
            'faculty_id.exists' =>
                'Fakultas tidak tersedia atau sudah nonaktif.',

            'study_program_id.required' =>
                'Program studi wajib dipilih.',
            'study_program_id.integer' =>
                'Program studi yang dipilih tidak valid.',
            'study_program_id.exists' =>
                'Program studi tidak tersedia, sudah nonaktif, atau tidak sesuai dengan fakultas.',

            'file_sertifikat.required' =>
                'Berkas sertifikat HAKI wajib diunggah.',
            'file_sertifikat.file' =>
                'Berkas sertifikat harus berupa file yang valid.',
            'file_sertifikat.mimes' =>
                'Format sertifikat harus PDF, JPG, JPEG, atau PNG.',
            'file_sertifikat.max' =>
                'Ukuran file sertifikat maksimal 3 MB.',
        ];
    }
}
