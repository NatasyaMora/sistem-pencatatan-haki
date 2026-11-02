<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Haki;
use App\Models\RenewalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class AdminRenewalRequestController extends Controller
{
    /**
     * Menampilkan daftar pengajuan pembaruan HAKI.
     */
    public function index(Request $request): View
    {
        $query = RenewalRequest::with(['haki', 'user'])->latest();

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
     * Mengonfirmasi pengajuan dan memperbarui data HAKI.
     */
    public function confirm(
        Request $request,
        RenewalRequest $renewalRequest
    ): RedirectResponse {
        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldHakiFile = null;
        $newHakiFile = null;
        $privateRenewalFile = null;
        $legacyPublicRenewalFile = null;
        $createdPrivateFiles = [];

        try {
            DB::transaction(function () use (
                $renewalRequest,
                $validated,
                &$oldHakiFile,
                &$newHakiFile,
                &$privateRenewalFile,
                &$legacyPublicRenewalFile,
                &$createdPrivateFiles
            ) {
                $pending = RenewalRequest::query()
                    ->whereKey($renewalRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pending->status !== 'menunggu') {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Pengajuan ini sudah diproses sebelumnya.',
                    ]);
                }

                $haki = Haki::query()
                    ->whereKey($pending->haki_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $oldHakiFile = $haki->file_sertifikat;

                /*
                 * Jika tersedia, salin berkas lampiran pembaruan
                 * ke penyimpanan privat dengan nama yang aman.
                 *
                 * Berkas lampiran tetap dipertahankan sebagai riwayat.
                 */
                $renewalFilePath = $pending->file_sertifikat_baru;

                if ($renewalFilePath !== null) {
                    if (!$this->isSafePath(
                        $renewalFilePath,
                        'renewal-certificates'
                    )) {
                        throw ValidationException::withMessages([
                            'file_sertifikat_baru' =>
                                'Lokasi berkas lampiran tidak valid.',
                        ]);
                    }

                    $localDisk = Storage::disk('local');
                    $publicDisk = Storage::disk('public');

                    if ($localDisk->exists($renewalFilePath)) {
                        $privateRenewalFile = $renewalFilePath;
                    } elseif ($publicDisk->exists($renewalFilePath)) {
                        // Migrasi lampiran lama dari disk public.
                        $extension = pathinfo(
                            $renewalFilePath,
                            PATHINFO_EXTENSION
                        );

                        $privateRenewalFile =
                            'renewal-certificates/' . Str::uuid()
                            . ($extension !== '' ? '.' . $extension : '');

                        $contents = $publicDisk->get($renewalFilePath);

                        if (!$localDisk->put(
                            $privateRenewalFile,
                            $contents
                        )) {
                            throw new RuntimeException(
                                'Gagal memindahkan lampiran pembaruan ke penyimpanan privat.'
                            );
                        }

                        $createdPrivateFiles[] = $privateRenewalFile;

                        $legacyPublicRenewalFile = $renewalFilePath;

                        $pending->file_sertifikat_baru =
                            $privateRenewalFile;
                    } else {
                        throw ValidationException::withMessages([
                            'file_sertifikat_baru' =>
                                'Lampiran sertifikat tidak ditemukan. Periksa berkas sebelum mengonfirmasi pengajuan.',
                        ]);
                    }

                    /*
                     * Simpan salinan sebagai sertifikat HAKI terbaru.
                     * Dengan demikian, path sertifikat utama konsisten
                     * menggunakan direktori certificates/.
                     */
                    $extension = pathinfo(
                        $privateRenewalFile,
                        PATHINFO_EXTENSION
                    );

                    $newHakiFile =
                        'certificates/' . Str::uuid()
                        . ($extension !== '' ? '.' . $extension : '');

                    if (!$localDisk->copy(
                        $privateRenewalFile,
                        $newHakiFile
                    )) {
                        throw new RuntimeException(
                            'Gagal menyalin sertifikat pembaruan ke penyimpanan utama.'
                        );
                    }

                    $createdPrivateFiles[] = $newHakiFile;
                    $newHakiFile = $newHakiFile;
                    $haki->file_sertifikat = $newHakiFile;
                }

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

                $haki->save();

                $pending->status = 'dikonfirmasi';
                $pending->catatan_admin =
                    $validated['catatan_admin'] ?? null;
                $pending->diproses_pada = now();
                $pending->save();
            });
        } catch (Throwable $exception) {
            // Hapus salinan baru jika transaksi gagal.
            foreach (array_unique($createdPrivateFiles) as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }

        /*
         * Hapus sertifikat utama sebelumnya setelah perubahan database
         * berhasil. Lampiran pembaruan tetap disimpan sebagai riwayat.
         */
        if (
            $newHakiFile !== null
            && $oldHakiFile
            && $oldHakiFile !== $newHakiFile
            && $oldHakiFile !== $privateRenewalFile
        ) {
            $this->deleteCertificateFromBothDisks($oldHakiFile);
        }

        // Hapus salinan lampiran lama yang sudah dipindahkan ke privat.
        if ($legacyPublicRenewalFile !== null) {
            Storage::disk('public')->delete(
                $legacyPublicRenewalFile
            );
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
                    'status' =>
                        'Pengajuan ini sudah diproses sebelumnya.',
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

    /**
     * Memastikan path hanya berada di direktori sertifikat yang dikenal.
     */
    private function isSafePath(
        string $path,
        string $directory
    ): bool {
        return str_starts_with($path, $directory . '/')
            && !str_contains($path, '..')
            && !str_contains($path, '\\')
            && !str_contains($path, "\0");
    }

    /**
     * Menghapus berkas sertifikat lama dari disk privat dan publik.
     */
    private function deleteCertificateFromBothDisks(
        string $path
    ): void {
        $isCertificate = $this->isSafePath(
            $path,
            'certificates'
        );

        $isRenewalCertificate = $this->isSafePath(
            $path,
            'renewal-certificates'
        );

        if (!$isCertificate && !$isRenewalCertificate) {
            return;
        }

        Storage::disk('local')->delete($path);
        Storage::disk('public')->delete($path);
    }
}
