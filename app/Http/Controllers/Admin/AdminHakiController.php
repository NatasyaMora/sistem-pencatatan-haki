<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateHakiRequest;
use App\Models\Haki;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminHakiController extends Controller
{
    /**
     * Menampilkan daftar seluruh HAKI untuk Admin dengan pencarian, filter, dan pagination.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $jenis = $request->query('jenis');
        $tahun = $request->query('tahun');

        // Query utama dengan eager loading user
        $query = Haki::with('user');

        // Fitur Pencarian: judul HAKI, nomor sertifikat, nama pemilik, NIM/NIP pemilik
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_haki', 'like', '%' . $search . '%')
                  ->orWhere('nomor_sertifikat', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('identity_number', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter Jenis HAKI
        if (!empty($jenis)) {
            $query->where('jenis_haki', $jenis);
        }

        // Filter Tahun
        if (!empty($tahun)) {
            $query->where('tahun', $tahun);
        }

        // Ambil daftar tahun unik yang tersedia di database, diurutkan dari terbaru ke terlama
        $tahunList = Haki::select('tahun')
            ->distinct()
            ->whereNotNull('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Pilihan Jenis HAKI
        $jenisOptions = [
            'Hak Cipta',
            'Paten',
            'Merek',
            'Desain Industri',
            'Rahasia Dagang',
            'Indikasi Geografis',
            'Lainnya',
        ];

        // Pagination 10 data per halaman & pertahankan parameter query string
        $hakis = $query->latest()->paginate(10)->withQueryString();

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
     * Menampilkan detail satu data HAKI untuk Admin.
     */
    public function show(Haki $haki): View
    {
        $haki->load('user');

        return view('admin.haki.show', compact('haki'));
    }

    /**
     * Menampilkan form edit data HAKI.
     */
    public function edit(Haki $haki): View
    {
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

        return view('admin.haki.edit', compact('haki', 'jenisOptions'));
    }

    /**
     * Memperbarui data HAKI yang ada di database.
     */
    public function update(UpdateHakiRequest $request, Haki $haki): RedirectResponse
    {
        $validated = $request->validated();

        $updateData = [
            'judul_haki' => $validated['judul_haki'],
            'jenis_haki' => $validated['jenis_haki'],
            'nomor_sertifikat' => $validated['nomor_sertifikat'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tahun' => $validated['tahun'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ];

        // Penanganan penggantian file sertifikat (opsional)
        if ($request->hasFile('file_sertifikat')) {
            $newPath = $request->file('file_sertifikat')->store('certificates', 'public');

            // Hapus file lama dari storage jika ada dan file baru sudah tersimpan
            if ($haki->file_sertifikat && Storage::disk('public')->exists($haki->file_sertifikat)) {
                Storage::disk('public')->delete($haki->file_sertifikat);
            }

            $updateData['file_sertifikat'] = $newPath;
        }

        // user_id TIDAK PERNAH diupdate
        $haki->update($updateData);

        return redirect()->route('admin.haki.show', $haki)
            ->with('success', 'Data HAKI berhasil diperbarui.');
    }
}
