<?php

namespace App\Http\Requests;

use App\Models\Haki;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreRenewalRequest extends FormRequest
{
    /**
     * Memastikan pengguna hanya mengajukan pembaruan
     * untuk HAKI miliknya sendiri.
     */
    public function authorize(): bool
    {
        $haki = $this->route('haki');
        $user = $this->user();

        return $haki instanceof Haki
            && $user !== null
            && (int) $haki->user_id === (int) $user->id;
    }

    /**
     * Aturan validasi pengajuan pembaruan HAKI.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $haki = $this->route('haki');

        $tanggalBerlakuRules = [
            'required',
            'date',
            'after:today',
        ];

        // Jika masa berlaku lama tersedia, tanggal baru
        // juga harus berada setelah tanggal tersebut.
        if ($haki instanceof Haki && $haki->tanggal_kadaluarsa) {
            $tanggalLama = Carbon::parse(
                $haki->tanggal_kadaluarsa
            )->toDateString();

            $tanggalBerlakuRules[] = 'after:' . $tanggalLama;
        }

        return [
            'tanggal_kadaluarsa_baru' => $tanggalBerlakuRules,

            'nomor_sertifikat_baru' => [
                'nullable',
                'string',
                'max:255',
            ],

            'file_sertifikat_baru' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:3072',
            ],

            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal_kadaluarsa_baru.required' =>
                'Masa berlaku baru wajib diisi.',

            'tanggal_kadaluarsa_baru.date' =>
                'Masa berlaku baru harus berupa tanggal yang valid.',

            'tanggal_kadaluarsa_baru.after' =>
                'Masa berlaku baru harus setelah hari ini dan setelah masa berlaku sebelumnya jika ada.',

            'nomor_sertifikat_baru.max' =>
                'Nomor sertifikat maksimal 255 karakter.',

            'file_sertifikat_baru.file' =>
                'Berkas sertifikat tidak valid.',

            'file_sertifikat_baru.mimes' =>
                'Berkas sertifikat harus berupa PDF, JPG, JPEG, atau PNG.',

            'file_sertifikat_baru.max' =>
                'Ukuran berkas sertifikat maksimal 3 MB.',

            'catatan.string' =>
                'Catatan harus berupa teks.',

            'catatan.max' =>
                'Catatan maksimal 1000 karakter.',
        ];
    }
}
