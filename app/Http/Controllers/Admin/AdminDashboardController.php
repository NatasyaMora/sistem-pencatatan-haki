<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Haki;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Admin HAKIKU.
     */
    public function index(Request $request): View
    {
        $admin = $request->user();

        // 1. Total seluruh HAKI
        $totalHaki = Haki::count();

        // 2. Total HAKI dipublikasikan (is_public = true)
        $totalPublic = Haki::where('is_public', true)->count();

        // 3. Total HAKI belum dipublikasikan (is_public = false)
        $totalNonPublic = Haki::where('is_public', false)->count();

        // 4. Total mahasiswa yang memiliki HAKI
        $totalMahasiswaPemilik = User::where('user_type', 'mahasiswa')
            ->whereHas('hakis')
            ->count();

        // 5. Total dosen yang memiliki HAKI
        $totalDosenPemilik = User::where('user_type', 'dosen')
            ->whereHas('hakis')
            ->count();

        // 6. Ringkasan jumlah HAKI berdasarkan jenis_haki
        $jenisStats = Haki::selectRaw('jenis_haki, count(*) as total')
            ->groupBy('jenis_haki')
            ->pluck('total', 'jenis_haki');

        return view('admin.dashboard', compact(
            'admin',
            'totalHaki',
            'totalPublic',
            'totalNonPublic',
            'totalMahasiswaPemilik',
            'totalDosenPemilik',
            'jenisStats'
        ));
    }
}
