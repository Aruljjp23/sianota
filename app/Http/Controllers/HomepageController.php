<?php

namespace App\Http\Controllers;

use App\Models\Legalisasi;
use App\Models\Skmht;
use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{
    public function homepage()
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'karyawan',
            403
        );

        $totalSkmht = Skmht::count();
        $totalLegalisasi = Legalisasi::count();

        return view('karyawan.homepage', compact(
            'totalSkmht',
            'totalLegalisasi'
        ));
    }
}