<?php

namespace App\Http\Controllers;

use App\Models\Legalisasi;
use App\Models\Skmht;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LaporanController extends Controller
{
    private function adminOnly(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'admin',
            403
        );
    }

    private function queryLaporan(Request $request, string $jenis): Builder
    {
        $model = $jenis === 'skmht' ? Skmht::class : Legalisasi::class;

        $kolomTanggal = $jenis === 'skmht' ? 'tanggal_akta' : 'tanggal';
        $kolomNomor = $jenis === 'skmht' ? 'nomor_bulanan' : 'no_urut';
        $kolomSifat = $jenis === 'skmht' ? 'sifat_jenis_akta' : 'sifat_surat';

        $query = $model::query()->with('penghadap');

        if ($request->filled('search')) {
            $keyword = trim($request->input('search'));

            $query->where(function ($q) use (
                $keyword,
                $kolomNomor,
                $kolomSifat,
                $jenis
            ) {
                $q->where($kolomNomor, 'like', "%{$keyword}%")
                    ->orWhere($kolomSifat, 'like', "%{$keyword}%")
                    ->orWhereHas('penghadap', function ($penghadap) use ($keyword) {
                        $penghadap->where(
                            'nama_penghadap',
                            'like',
                            "%{$keyword}%"
                        );
                    });

                if ($jenis === 'skmht') {
                    $q->orWhere('alamat', 'like', "%{$keyword}%");
                }
            });
        }

        if ($request->filled('sifat')) {
            $query->where($kolomSifat, $request->input('sifat'));
        }

        if ($request->filled('tahun')) {
            $query->whereYear(
                $kolomTanggal,
                (int) $request->input('tahun')
            );
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                $kolomTanggal,
                '>=',
                $request->input('tanggal_mulai')
            );
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(
                $kolomTanggal,
                '<=',
                $request->input('tanggal_selesai')
            );
        }

        return $query;
    }
    
    private function ambilLaporan(
        Request $request,
        string $jenis,
        bool $paginate = true
    ) {
        $kolomTanggal = $jenis === 'skmht' ? 'tanggal_akta' : 'tanggal';
        $kolomNomor = $jenis === 'skmht' ? 'nomor_bulanan' : 'no_urut';
        $kolomSifat = $jenis === 'skmht' ? 'sifat_jenis_akta' : 'sifat_surat';
        $primaryKey = $jenis === 'skmht' ? 'id_skmht' : 'id_legalisasi';

        $query = $this->queryLaporan($request, $jenis)
            ->orderByDesc($kolomTanggal)
            ->orderByDesc($primaryKey);

        $formatData = function ($item) use (
            $jenis,
            $kolomTanggal,
            $kolomNomor,
            $kolomSifat
        ) {
            $tanggal = $item->{$kolomTanggal};

            $namaPenghadap = $item->penghadap
                ->pluck('nama_penghadap')
                ->filter()
                ->implode(', ');

            return [
                'nomor' => $item->{$kolomNomor} ?? '-',
                'tanggal' => $tanggal
                    ? Carbon::parse($tanggal)->format('d-m-Y')
                    : '-',
                'sifat' => $item->{$kolomSifat} ?? '-',
                'alamat' => $jenis === 'skmht'
                    ? ($item->alamat ?: '-')
                    : '-',
                'penghadap' => $namaPenghadap ?: '-',
            ];
        };

        if (!$paginate) {
            return $query->get()->map($formatData);
        }

        return $query->paginate(10)
            ->withQueryString()
            ->through($formatData);
    }

    public function data_laporan(Request $request)
    {
        $this->adminOnly();

        $request->validate([
            'jenis' => ['nullable', 'in:skmht,legalisasi'],
            'search' => ['nullable', 'string', 'max:150'],
            'sifat' => ['nullable', 'string', 'max:150'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:tanggal_mulai',
            ],
        ], [
            'tanggal_selesai.after_or_equal' =>
                'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $jenis = $request->input('jenis', 'skmht');

        $model = $jenis === 'skmht' ? Skmht::class : Legalisasi::class;
        $kolomSifat = $jenis === 'skmht'
            ? 'sifat_jenis_akta'
            : 'sifat_surat';
        $kolomTanggal = $jenis === 'skmht'
            ? 'tanggal_akta'
            : 'tanggal';

        $daftarSifat = $model::query()
            ->whereNotNull($kolomSifat)
            ->where($kolomSifat, '!=', '')
            ->distinct()
            ->orderBy($kolomSifat)
            ->pluck($kolomSifat);

        $daftarTahun = $model::query()
            ->whereNotNull($kolomTanggal)
            ->selectRaw("YEAR({$kolomTanggal}) AS tahun")
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $laporan = $this->ambilLaporan($request, $jenis);

        return view('admin.laporan.data_laporan', compact(
            'jenis',
            'laporan',
            'daftarSifat',
            'daftarTahun'
        ));
    }

    public function pdf(Request $request)
    {
        $this->adminOnly();

        $request->validate([
            'jenis' => ['nullable', 'in:skmht,legalisasi'],
            'search' => ['nullable', 'string', 'max:150'],
            'sifat' => ['nullable', 'string', 'max:150'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:tanggal_mulai',
            ],
        ], [
            'tanggal_selesai.after_or_equal' =>
                'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $jenis = $request->input('jenis', 'skmht');

        $laporan = $this->ambilLaporan($request, $jenis, false);

        $judul = $jenis === 'skmht'
            ? 'LAPORAN DATA SKMHT'
            : 'LAPORAN DATA LEGALISASI';

        $pdf = Pdf::loadView('admin.laporan.cetak_pdf', compact(
            'jenis',
            'laporan',
            'judul'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-' . $jenis . '.pdf');
    }
}