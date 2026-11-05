<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateHakiRequest;
use App\Models\Haki;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AdminHakiController extends Controller
{
    /**
     * Menampilkan daftar seluruh HAKI dengan pencarian,
     * filter, dan pagination.
     */
    public function index(Request $request): View
    {
        Haki::syncExpiredStatuses();

        $search = $request->query('search');
        $jenis = $request->query('jenis');
        $tahun = $request->query('tahun');

        $query = Haki::with('user');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_haki', 'like', '%' . $search . '%')
                    ->orWhere('nomor_sertifikat', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere(
                                'identity_number',
                                'like',
                                '%' . $search . '%'
                            );
                    });
            });
        }

        if (!empty($jenis)) {
            $query->where('jenis_haki', $jenis);
        }

        if (!empty($tahun)) {
            $query->where('tahun', $tahun);
        }

        $tahunList = Haki::select('tahun')
            ->distinct()
            ->whereNotNull('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $jenisOptions = [
            'Hak Cipta',
            'Paten',
            'Merek',
            'Desain Industri',
            'Rahasia Dagang',
            'Indikasi Geografis',
            'Lainnya',
        ];

        $hakis = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.haki.index', compact(
            'hakis',
            'search',
            'jenis',
            'tahun',
            'tahunList',
            'jenisOptions'
        ));
    }

    /**
     * Menampilkan detail HAKI untuk Admin.
     */
    public function show(Haki $haki): View
    {
        Haki::syncExpiredStatuses();
        $haki->refresh();
        $haki->load(['user', 'jenisHaki', 'faculty', 'studyProgram']);

        return view('admin.haki.show', compact('haki'));
    }

    /**
     * Menampilkan form edit HAKI.
     */
    public function edit(Haki $haki): View
    {
        Haki::syncExpiredStatuses();
        $haki->refresh();
        $haki->load('user');

        $jenisOptions = [
            'Hak Cipta',
            'Paten',
            'Merek',
            'Desain Industri',
            'Rahasia Dagang',
            'Indikasi Geografis',
            'Lainnya',
        ];

        return view('admin.haki.edit', compact(
            'haki',
            'jenisOptions'
        ));
    }

    /**
     * Memperbarui data HAKI.
     *
     * Sertifikat pengganti disimpan di penyimpanan privat.
     */
    public function update(
        UpdateHakiRequest $request,
        Haki $haki
    ): RedirectResponse {
        $validated = $request->validated();
        $today = now('Asia/Jakarta')->toDateString();

        $updateData = [
            'judul_haki' => $validated['judul_haki'],
            'jenis_haki' => $validated['jenis_haki'],
            'nomor_sertifikat' => $validated['nomor_sertifikat'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'],
            'tahun' => $validated['tahun'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ];

        // Jika tanggal kedaluwarsa sudah lewat, otomatis nonaktifkan
        if ($validated['tanggal_kadaluarsa'] < $today) {
            $updateData['status'] = 'nonaktif';
            $updateData['is_public'] = false;
        }

        $oldFilePath = $haki->file_sertifikat;
        $newFilePath = null;

        // Simpan berkas pengganti di storage/app/private/certificates.
        if ($request->hasFile('file_sertifikat')) {
            $newFilePath = $request->file('file_sertifikat')
                ->store('certificates', 'local');

            if (!$newFilePath) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'file_sertifikat' =>
                            'Sertifikat gagal disimpan. Silakan coba kembali.',
                    ]);
            }

            $updateData['file_sertifikat'] = $newFilePath;
        }

        try {
            // Pemilik HAKI tidak diubah oleh proses ini.
            $haki->update($updateData);
        } catch (Throwable $exception) {
            // Hapus berkas baru jika pembaruan database gagal.
            if ($newFilePath !== null) {
                Storage::disk('local')->delete($newFilePath);
            }

            throw $exception;
        }

        // Hapus berkas lama hanya setelah database berhasil diperbarui.
        // Periksa kedua disk karena file lama mungkin berada di public,
        // sedangkan file terbaru berada di private.
        if (
            $newFilePath !== null &&
            $oldFilePath &&
            $oldFilePath !== $newFilePath
        ) {
            $this->deleteOldCertificate($oldFilePath);
        }

        return redirect()
            ->route('admin.haki.show', $haki)
            ->with('success', 'Data HAKI berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif / nonaktif HAKI.
     */
    public function toggleStatus(Haki $haki): RedirectResponse
    {
        $today = now('Asia/Jakarta')->toDateString();

        if ($haki->status === 'aktif') {
            $haki->update([
                'status' => 'nonaktif',
                'is_public' => false,
            ]);

            return back()->with('success', "Status HAKI '{$haki->judul_haki}' berhasil dinonaktifkan.");
        }

        // Jika akan diaktifkan kembali, pastikan tanggal kedaluwarsa masih berlaku
        if (empty($haki->tanggal_kadaluarsa) || $haki->tanggal_kadaluarsa->toDateString() < $today) {
            return back()->with('error', 'HAKI yang sudah kedaluwarsa atau belum memiliki tanggal kedaluwarsa tidak dapat diaktifkan kembali.');
        }

        $haki->update([
            'status' => 'aktif',
            'is_public' => true,
        ]);

        return back()->with('success', "Status HAKI '{$haki->judul_haki}' berhasil diaktifkan.");
    }

    /**
     * Mengaktifkan HAKI yang belum kedaluwarsa.
     */
    public function activate(Haki $haki): RedirectResponse
    {
        $today = now('Asia/Jakarta')->toDateString();

        if (empty($haki->tanggal_kadaluarsa) || $haki->tanggal_kadaluarsa->toDateString() < $today) {
            return back()->with('error', 'HAKI yang sudah kedaluwarsa atau belum memiliki tanggal kedaluwarsa tidak dapat diaktifkan kembali.');
        }

        $haki->update([
            'status' => 'aktif',
            'is_public' => true,
        ]);

        return back()->with('success', "Status HAKI '{$haki->judul_haki}' berhasil diaktifkan.");
    }

    /**
     * Menonaktifkan HAKI.
     */
    public function deactivate(Haki $haki): RedirectResponse
    {
        $haki->update([
            'status' => 'nonaktif',
            'is_public' => false,
        ]);

        return back()->with('success', "Status HAKI '{$haki->judul_haki}' berhasil dinonaktifkan.");
    }

    /**
     * Menghapus berkas lama dari disk lokal dan publik.
     */
    private function deleteOldCertificate(string $path): void
    {
        // Batasi penghapusan pada direktori sertifikat yang dikenal.
        $allowed = str_starts_with($path, 'certificates/')
            && !str_contains($path, '..')
            && !str_contains($path, '\\')
            && !str_contains($path, "\0");

        if (!$allowed) {
            return;
        }

        Storage::disk('local')->delete($path);
        Storage::disk('public')->delete($path);
    }
}
