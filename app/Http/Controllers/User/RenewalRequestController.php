<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRenewalRequest;
use App\Models\Haki;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RenewalRequestController extends Controller
{
    /**
    * Menampilkan riwayat pengajuan pembaruan milik pengguna yang login.
    */
    public function index(\Illuminate\Http\Request $request): \Illuminate\View\View
    {
       $renewalRequests = $request->user()
           ->renewalRequests()
           ->with('haki')
           ->latest()
           ->paginate(10);

        return view('user.renewal.index', compact('renewalRequests'));
    }

    /**
     * Menampilkan form pengajuan renewal HAKI.
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
     * Menyimpan pengajuan renewal HAKI.
     */
    public function store(
        StoreRenewalRequest $request,
        Haki $haki
    ): RedirectResponse {
        $this->authorizeHakiOwner($haki);

        // Cegah user mengirim pengajuan baru
        // jika masih ada pengajuan yang sedang menunggu.
        $hasPendingRequest = $haki->renewalRequests()
            ->where('status', 'menunggu')
            ->exists();

        if ($hasPendingRequest) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Masih ada pengajuan renewal yang sedang menunggu proses Admin.'
                );
        }

        $validated = $request->validated();

        $filePath = null;

        // Upload sertifikat baru jika user menyertakannya.
        if ($request->hasFile('file_sertifikat_baru')) {
            $filePath = $request->file('file_sertifikat_baru')
                ->store('renewal-certificates', 'public');
        }

        // Membuat riwayat/pengajuan renewal baru.
        $haki->renewalRequests()->create([
            'user_id' => auth()->id(),
            'tanggal_kadaluarsa_baru' => $validated['tanggal_kadaluarsa_baru'],
            'nomor_sertifikat_baru' => $validated['nomor_sertifikat_baru'] ?? null,
            'file_sertifikat_baru' => $filePath,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'menunggu',
        ]);

        return redirect()
            ->route('user.haki.index')
            ->with(
                'success',
                'Pengajuan renewal HAKI berhasil dikirim dan menunggu konfirmasi Admin.'
            );
    }

    /**
     * Memastikan HAKI adalah milik user yang sedang login.
     */
    private function authorizeHakiOwner(Haki $haki): void
    {
        abort_unless(
            $haki->user_id === auth()->id(),
            403
        );
    }
}