<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJenisHakiRequest;
use App\Http\Requests\UpdateJenisHakiRequest;
use App\Models\JenisHaki;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminJenisHakiController extends Controller
{
    /**
     * Menampilkan daftar master Jenis HAKI.
     */
    public function index(): View
    {
        $jenisHakis = JenisHaki::withCount('hakis')->latest()->get();

        return view('admin.jenis-haki.index', compact('jenisHakis'));
    }

    /**
     * Menampilkan form tambah Jenis HAKI.
     */
    public function create(): View
    {
        return view('admin.jenis-haki.create');
    }

    /**
     * Menyimpan Jenis HAKI baru.
     */
    public function store(StoreJenisHakiRequest $request): RedirectResponse
    {
        JenisHaki::create([
            'nama' => $request->validated('nama'),
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('admin.jenis-haki.index')
            ->with('success', 'Jenis HAKI berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit Jenis HAKI.
     */
    public function edit(JenisHaki $jenisHaki): View
    {
        return view('admin.jenis-haki.edit', compact('jenisHaki'));
    }

    /**
     * Memperbarui data Jenis HAKI.
     */
    public function update(UpdateJenisHakiRequest $request, JenisHaki $jenisHaki): RedirectResponse
    {
        $jenisHaki->update([
            'nama' => $request->validated('nama'),
        ]);

        return redirect()
            ->route('admin.jenis-haki.index')
            ->with('success', 'Jenis HAKI berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif / nonaktif master Jenis HAKI.
     */
    public function toggleStatus(JenisHaki $jenisHaki): RedirectResponse
    {
        $newStatus = $jenisHaki->status === 'aktif' ? 'nonaktif' : 'aktif';

        $jenisHaki->update([
            'status' => $newStatus,
        ]);

        $label = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.jenis-haki.index')
            ->with('success', "Jenis HAKI '{$jenisHaki->nama}' berhasil {$label}.");
    }
}
