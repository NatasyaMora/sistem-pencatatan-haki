<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudyProgramRequest;
use App\Http\Requests\UpdateStudyProgramRequest;
use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminStudyProgramController extends Controller
{
    /**
     * Menampilkan daftar master Program Studi.
     */
    public function index(): View
    {
        $studyPrograms = StudyProgram::with('faculty')->latest()->get();

        return view('admin.program-studi.index', compact('studyPrograms'));
    }

    /**
     * Menampilkan form tambah Program Studi.
     */
    public function create(): View
    {
        $faculties = Faculty::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.program-studi.create', compact('faculties'));
    }

    /**
     * Menyimpan Program Studi baru.
     */
    public function store(StoreStudyProgramRequest $request): RedirectResponse
    {
        StudyProgram::create([
            'faculty_id' => $request->validated('faculty_id'),
            'nama' => $request->validated('nama'),
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('admin.program-studi.index')
            ->with('success', 'Program Studi berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit Program Studi.
     */
    public function edit(StudyProgram $studyProgram): View
    {
        $faculties = Faculty::where('status', 'aktif')
            ->orWhere('id', $studyProgram->faculty_id)
            ->orderBy('nama')
            ->get();

        return view('admin.program-studi.edit', compact('studyProgram', 'faculties'));
    }

    /**
     * Memperbarui data Program Studi.
     */
    public function update(UpdateStudyProgramRequest $request, StudyProgram $studyProgram): RedirectResponse
    {
        $studyProgram->update([
            'faculty_id' => $request->validated('faculty_id'),
            'nama' => $request->validated('nama'),
        ]);

        return redirect()
            ->route('admin.program-studi.index')
            ->with('success', 'Program Studi berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif / nonaktif Program Studi.
     */
    public function toggleStatus(StudyProgram $studyProgram): RedirectResponse
    {
        $newStatus = $studyProgram->status === 'aktif' ? 'nonaktif' : 'aktif';

        $studyProgram->update([
            'status' => $newStatus,
        ]);

        $label = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.program-studi.index')
            ->with('success', "Program Studi '{$studyProgram->nama}' berhasil {$label}.");
    }
}
