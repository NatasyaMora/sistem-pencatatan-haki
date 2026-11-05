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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class MyHakiController extends Controller
{
    /**
     * Menampilkan daftar HAKI milik pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        Haki::syncExpiredStatuses();

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
        Haki::syncExpiredStatuses();

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
     * Menampilkan detail HAKI milik pengguna yang sedang login.
     *
     * Pengguna hanya boleh melihat HAKI miliknya sendiri.
     * Akses ke HAKI milik pengguna lain menghasilkan 404 (Not Found).
     */
    public function show(Request $request, int $id): View
    {
        Haki::syncExpiredStatuses();

        $user = $request->user();

        $haki = $user->hakis()
            ->with(['jenisHaki', 'faculty', 'studyProgram'])
            ->findOrFail($id);

        return view('user.my-haki.show', compact('user', 'haki'));
    }

    /**
     * Menyimpan pencatatan HAKI baru.
     *
     * Sertifikat disimpan pada disk privat, bukan disk public.
     */
    public function store(StoreHakiRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $file = $request->file('file_sertifikat');

        if (!$file) {
            return back()
                ->withInput()
                ->withErrors([
                    'file_sertifikat' =>
                        'File sertifikat wajib diunggah.',
                ]);
        }

        // Pastikan jenis HAKI tersedia.
        $jenisHaki = JenisHaki::where('status', 'aktif')
            ->findOrFail($validated['jenis_haki_id']);

        // Simpan sertifikat secara privat:
        // storage/app/private/certificates/
        $filePath = $file->store('certificates', 'local');

        if (!$filePath) {
            return back()
                ->withInput()
                ->withErrors([
                    'file_sertifikat' =>
                        'Sertifikat gagal disimpan. Silakan coba kembali.',
                ]);
        }

        // Tentukan status dan publikasi berdasarkan tanggal kedaluwarsa.
        $today = now('Asia/Jakarta')->toDateString();
        $isActive = $validated['tanggal_kadaluarsa'] >= $today;
        $status = $isActive ? 'aktif' : 'nonaktif';
        $isPublic = $isActive;

        try {
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
                'is_public' => $isPublic,
            ]);
        } catch (Throwable $exception) {
            // Hapus berkas baru apabila penyimpanan data gagal,
            // supaya tidak meninggalkan berkas tanpa catatan HAKI.
            Storage::disk('local')->delete($filePath);

            throw $exception;
        }

        return redirect()
            ->route('user.haki.index')
            ->with('success', 'Data HAKI berhasil ditambahkan.');
    }
}
