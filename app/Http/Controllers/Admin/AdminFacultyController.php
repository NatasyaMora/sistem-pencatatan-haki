<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminFacultyController extends Controller
{
    /**
     * Menampilkan daftar master Fakultas.
     */
    public function index(): View
    {
        $faculties = Faculty::withCount('studyPrograms')->latest()->get();

        return view('admin.fakultas.index', compact('faculties'));
    }

    /**
     * Menampilkan form tambah Fakultas.
     */
    public function create(): View
    {
        return view('admin.fakultas.create');
    }

    /**
     * Menyimpan Fakultas baru.
     */
    public function store(StoreFacultyRequest $request): RedirectResponse
    {
        Faculty::create([
            'nama' => $request->validated('nama'),
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit Fakultas.
     */
    public function edit(Faculty $faculty): View
    {
        return view('admin.fakultas.edit', compact('faculty'));
    }

    /**
     * Memperbarui data Fakultas.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty): RedirectResponse
    {
        $faculty->update([
            'nama' => $request->validated('nama'),
        ]);

        return redirect()
            ->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif / nonaktif Fakultas.
     */
    public function toggleStatus(Faculty $faculty): RedirectResponse
    {
        $newStatus = $faculty->status === 'aktif' ? 'nonaktif' : 'aktif';

        $faculty->update([
            'status' => $newStatus,
        ]);

        $label = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.fakultas.index')
            ->with('success', "Fakultas '{$faculty->nama}' berhasil {$label}.");
    }
}
