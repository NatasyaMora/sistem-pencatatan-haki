<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Haki;
use App\Models\RenewalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminRenewalRequestController extends Controller
{
    /**
     * Menampilkan daftar pengajuan pembaruan HAKI.
     */
    public function index(Request $request): View
    {
        $query = RenewalRequest::with(['haki', 'user'])
            ->latest();

        $status = $request->query('status');

        if (in_array($status, ['menunggu', 'dikonfirmasi', 'ditolak'], true)) {
            $query->where('status', $status);
        }

        $renewalRequests = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.renewal-requests.index',
            compact('renewalRequests', 'status')
        );
    }

    /**
     * Menampilkan detail pengajuan pembaruan.
     */
    public function show(RenewalRequest $renewalRequest): View
    {
        $renewalRequest->load(['haki', 'user']);

        return view(
            'admin.renewal-requests.show',
            compact('renewalRequest')
        );
    }

    /**
     * Mengonfirmasi pengajuan dan memperbarui data HAKI lama.
     */
    public function confirm(
        Request $request,
        RenewalRequest $renewalRequest
    ): RedirectResponse {
        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldFilePath = null;
        $newFilePath = null;

        DB::transaction(function () use (
            $renewalRequest,
            $validated,
            &$oldFilePath,
            &$newFilePath
        ) {
            $pending = RenewalRequest::query()
                ->whereKey($renewalRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pending->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan ini sudah diproses sebelumnya.',
                ]);
            }

            $haki = Haki::query()
                ->whereKey($pending->haki_id)
                ->lockForUpdate()
                ->firstOrFail();

            $tanggalBaru = $pending->tanggal_kadaluarsa_baru
                ->toDateString();

            $haki->tanggal_kadaluarsa = $tanggalBaru;

            $haki->status = $tanggalBaru >= now()->toDateString()
                ? 'aktif'
                : 'nonaktif';

            if ($pending->nomor_sertifikat_baru !== null) {
                $haki->nomor_sertifikat =
                    $pending->nomor_sertifikat_baru;
            }

            if ($pending->file_sertifikat_baru !== null) {
                $oldFilePath = $haki->file_sertifikat;
                $newFilePath = $pending->file_sertifikat_baru;
                $haki->file_sertifikat = $newFilePath;
            }

            $haki->save();

            $pending->status = 'dikonfirmasi';
            $pending->catatan_admin = $validated['catatan_admin'] ?? null;
            $pending->diproses_pada = now();
            $pending->save();
        });

        // Hapus berkas sertifikat lama setelah perubahan database berhasil.
        if (
            $oldFilePath &&
            $oldFilePath !== $newFilePath
        ) {
            Storage::disk('public')->delete($oldFilePath);
        }

        return redirect()
            ->route('admin.renewal-requests.index')
            ->with(
                'success',
                'Pengajuan berhasil dikonfirmasi dan data HAKI telah diperbarui.'
            );
    }

    /**
     * Menolak pengajuan pembaruan HAKI.
     */
    public function reject(
        Request $request,
        RenewalRequest $renewalRequest
    ): RedirectResponse {
        $validated = $request->validate([
            'catatan_admin' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_admin.required' =>
                'Catatan penolakan wajib diisi.',
            'catatan_admin.max' =>
                'Catatan penolakan maksimal 1000 karakter.',
        ]);

        DB::transaction(function () use (
            $renewalRequest,
            $validated
        ) {
            $pending = RenewalRequest::query()
                ->whereKey($renewalRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pending->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan ini sudah diproses sebelumnya.',
                ]);
            }

            $pending->status = 'ditolak';
            $pending->catatan_admin = $validated['catatan_admin'];
            $pending->diproses_pada = now();
            $pending->save();
        });

        return redirect()
            ->route('admin.renewal-requests.index')
            ->with(
                'success',
                'Pengajuan pembaruan HAKI berhasil ditolak.'
            );
    }
}
