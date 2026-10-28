<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHakiRequest;
use App\Models\Faculty;
use App\Models\Haki;
use App\Models\JenisHaki;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyHakiController extends Controller
{
    /**
     * Menampilkan daftar HAKI milik user yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $hakis = $user->hakis()
            ->with(['jenisHaki', 'faculty', 'studyProgram'])
            ->latest()
            ->paginate(10);

        return view('user.my-haki.index', compact('user', 'hakis'));
    }

    /**
     * Menampilkan form pencatatan HAKI baru.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        $jenisOptions = JenisHaki::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $faculties = Faculty::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $studyPrograms = StudyProgram::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('user.my-haki.create', compact(
            'user',
            'jenisOptions',
            'faculties',
            'studyPrograms'
        ));
    }

    /**
     * Menyimpan pencatatan HAKI baru ke database.
     */
    public function store(StoreHakiRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Ambil jenis HAKI yang sudah divalidasi.
        $jenisHaki = JenisHaki::findOrFail(
            $validated['jenis_haki_id']
        );

        // Simpan file sertifikat.
        $filePath = $request->file('file_sertifikat')
            ->store('certificates', 'public');

        // Tentukan status otomatis berdasarkan tanggal kadaluarsa.
        $status = $validated['tanggal_kadaluarsa'] >= now()->toDateString()
            ? 'aktif'
            : 'nonaktif';

        Haki::create([
            'user_id' => $request->user()->id,
            'judul_haki' => $validated['judul_haki'],
            'jenis_haki_id' => $jenisHaki->id,
            'jenis_haki' => $jenisHaki->nama,
            'nomor_sertifikat' => $validated['nomor_sertifikat'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'],
            'tahun' => date(
                'Y',
                strtotime($validated['tanggal_terbit'])
            ),
            'faculty_id' => $validated['faculty_id'],
            'study_program_id' => $validated['study_program_id'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_sertifikat' => $filePath,
            'status' => $status,
            'is_public' => false,
        ]);

        return redirect()
            ->route('user.haki.index')
            ->with('success', 'Data HAKI berhasil ditambahkan.');
    }
}
