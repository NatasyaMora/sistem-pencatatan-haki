<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRenewalRequest;
use App\Models\Haki;
use App\Models\RenewalRequest as HakiRenewalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class RenewalRequestController extends Controller
{
    /**
     * Menampilkan riwayat pengajuan pembaruan milik pengguna.
     */
    public function index(Request $request): View
    {
        $renewalRequests = $request->user()
            ->renewalRequests()
            ->with('haki')
            ->latest()
            ->paginate(10);

        return view(
            'user.renewal.index',
            compact('renewalRequests')
        );
    }

    /**
     * Menampilkan form pengajuan pembaruan HAKI.
     */
    public function create(Haki $haki): View
    {
        $this->authorizeHakiOwner($haki);

        $hasPendingRequest = $haki->renewalRequests()
            ->where('status', 'menunggu')
            ->exists();

        return view('user.renewal.create', [
            'haki' => $haki,
            'hasPendingRequest' => $hasPendingRequest,
        ]);
    }

    /**
     * Menyimpan pengajuan pembaruan HAKI.
     */
    public function store(
        StoreRenewalRequest $request,
        Haki $haki
    ): RedirectResponse {
        $this->authorizeHakiOwner($haki);

        $validated = $request->validated();
        $filePath = null;

        // Simpan sertifikat baru di storage/app/private.
        if ($request->hasFile('file_sertifikat_baru')) {
            $filePath = $request->file('file_sertifikat_baru')
                ->store('renewal-certificates', 'local');

            if (!$filePath) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'file_sertifikat_baru' =>
                            'Sertifikat gagal disimpan. Silakan coba kembali.',
                    ]);
            }
        }

        try {
            $created = DB::transaction(function () use (
                $request,
                $haki,
                $validated,
                $filePath
            ) {
                // Kunci data HAKI untuk mencegah dua pengajuan
                // menunggu dibuat secara bersamaan.
                $lockedHaki = Haki::query()
                    ->whereKey($haki->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Periksa kembali kepemilikan HAKI.
                abort_unless(
                    (int) $lockedHaki->user_id ===
                    (int) $request->user()->id,
                    403
                );

                // Cegah pengajuan baru ketika masih ada
                // pengajuan yang menunggu proses Admin.
                $hasPendingRequest = $lockedHaki->renewalRequests()
                    ->where('status', 'menunggu')
                    ->exists();

                if ($hasPendingRequest) {
                    return false;
                }

                $lockedHaki->renewalRequests()->create([
                    'user_id' => $request->user()->id,
                    'tanggal_kadaluarsa_baru' =>
                        $validated['tanggal_kadaluarsa_baru'],
                    'nomor_sertifikat_baru' =>
                        $validated['nomor_sertifikat_baru'] ?? null,
                    'file_sertifikat_baru' => $filePath,
                    'catatan' => $validated['catatan'] ?? null,
                    'status' => 'menunggu',
                ]);

                return true;
            });
        } catch (Throwable $exception) {
            // Hapus file baru jika penyimpanan pengajuan gagal.
            if ($filePath !== null) {
                Storage::disk('local')->delete($filePath);
            }

            throw $exception;
        }

        if (!$created) {
            // Hapus file apabila pengajuan tidak dibuat karena
            // sudah ada pengajuan lain yang menunggu.
            if ($filePath !== null) {
                Storage::disk('local')->delete($filePath);
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Masih ada pengajuan pembaruan yang menunggu proses Admin.'
                );
        }

        return redirect()
            ->route('user.haki.index')
            ->with(
                'success',
                'Pengajuan pembaruan HAKI berhasil dikirim dan menunggu konfirmasi Admin.'
            );
    }

    /**
     * Memastikan HAKI merupakan milik pengguna yang login.
     */
    private function authorizeHakiOwner(Haki $haki): void
    {
        abort_unless(
            (int) $haki->user_id === (int) auth()->id(),
            403,
            'Kamu tidak memiliki akses ke data HAKI ini.'
        );
    }
}
