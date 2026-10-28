<?php

namespace App\Http\Controllers;

use App\Models\Haki;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Publik HAKIKU.
     */
    public function index(): View
    {
        $totalTercatat = Haki::count();
        $totalPublic = Haki::public()->count();

        // Statistik per jenis_haki untuk HAKI publik
        $jenisStats = Haki::public()
            ->selectRaw('jenis_haki, count(*) as total')
            ->groupBy('jenis_haki')
            ->pluck('total', 'jenis_haki');

        // Mengambil data HAKI publik saja (is_public = true)
        $publicHakis = Haki::public()
            ->with('user:id,name,identity_number,user_type')
            ->latest()
            ->paginate(10);

        return view('public.dashboard', compact(
            'totalTercatat',
            'totalPublic',
            'jenisStats',
            'publicHakis'
        ));
    }
}
