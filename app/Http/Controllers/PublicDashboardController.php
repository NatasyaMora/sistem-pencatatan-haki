<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Haki;
use App\Models\JenisHaki;
use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    /**
     * Filter HAKI yang boleh dihitung pada dashboard publik.
     */
    private function filterHakiPublikAktif(Builder $query): Builder
    {
        $today = now('Asia/Jakarta')->toDateString();

        return $query
            ->where('hakis.status', 'aktif')
            ->where('hakis.is_public', true)
            ->whereNotNull('hakis.tanggal_kadaluarsa')
            ->whereDate('hakis.tanggal_kadaluarsa', '>=', $today);
    }

    /**
     * Menampilkan Dashboard Publik HAKIKU.
     */
    public function index(): View
    {
        Haki::syncExpiredStatuses();

        // Total HAKI aktif, sudah dipublikasikan, dan belum kedaluwarsa.
        $totalHakiAktif = $this->filterHakiPublikAktif(
            Haki::query()
        )->count();

        // Menghitung jumlah jenis HAKI yang aktif pada master data.
        $totalJenisHaki = JenisHaki::where('status', 'aktif')->count();

        // Menghitung jumlah fakultas aktif.
        $totalFakultas = Faculty::where('status', 'aktif')->count();

        // Menghitung jumlah program studi aktif.
        $totalJurusan = StudyProgram::where('status', 'aktif')->count();

        // Statistik HAKI berdasarkan fakultas.
        $facultyStats = Faculty::query()
            ->where('status', 'aktif')
            ->withCount([
                'hakis as total_haki' => function (Builder $query) {
                    $this->filterHakiPublikAktif($query);
                },
            ])
            ->orderBy('nama')
            ->get()
            ->filter(fn ($faculty) => $faculty->total_haki > 0)
            ->sortByDesc('total_haki')
            ->values();

        // Statistik HAKI berdasarkan program studi.
        $studyProgramStats = StudyProgram::query()
            ->where('status', 'aktif')
            ->withCount([
                'hakis as total_haki' => function (Builder $query) {
                    $this->filterHakiPublikAktif($query);
                },
            ])
            ->orderBy('nama')
            ->get()
            ->filter(fn ($program) => $program->total_haki > 0)
            ->sortByDesc('total_haki')
            ->take(8)
            ->values();

        return view('public.dashboard', compact(
            'totalHakiAktif',
            'totalJenisHaki',
            'totalFakultas',
            'totalJurusan',
            'facultyStats',
            'studyProgramStats'
        ));
    }
}
