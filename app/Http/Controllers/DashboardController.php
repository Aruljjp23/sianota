<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skmht;
use App\Models\Legalisasi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'admin',
            403
        );

        $totalUser = User::count();
        $totalSkmht = Skmht::count();
        $totalLegalisasi = Legalisasi::count();

        
        $skmhtTerbaru = Skmht::with('penghadap')
            ->latest('tanggal_akta')
            ->latest('id_skmht')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'jenis' => 'SKMHT',
                    'nama' => $item->penghadap?->nama_penghadap ?? '-',
                    'tanggal' => $item->tanggal_akta
                        ? \Carbon\Carbon::parse($item->tanggal_akta)
                            ->format('d M Y')
                        : '-',
                    'urutan' => $item->tanggal_akta,
                ];
            });

        $legalisasiTerbaru = Legalisasi::with('penghadap')
            ->latest('tanggal')
            ->latest('id_legalisasi')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'jenis' => 'Legalisasi',
                    'nama' => $item->penghadap?->nama_penghadap ?? '-',
                    'tanggal' => $item->tanggal
                        ? \Carbon\Carbon::parse($item->tanggal)
                            ->format('d M Y')
                        : '-',
                    'urutan' => $item->tanggal,
                ];
            });

        $dokumenTerbaru = $skmhtTerbaru
            ->concat($legalisasiTerbaru)
            ->sortByDesc('urutan')
            ->take(5)
            ->values();

        return view('admin.dashboard', compact(
            'totalUser',
            'totalSkmht',
            'totalLegalisasi',
            'dokumenTerbaru'
        ));
    }
}