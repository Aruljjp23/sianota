<?php

namespace App\Http\Controllers;

use App\Models\Legalisasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LegalisasiController extends Controller
{
    private function adminOnly(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'admin',
            403
        );
    }

    private function karyawanOnly(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'karyawan',
            403
        );
    }

    private function filteredQuery(Request $request): Builder
    {
        return Legalisasi::with('penghadap')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('no_urut', 'like', "%{$search}%")
                        ->orWhere('sifat_surat', 'like', "%{$search}%")
                        ->orWhereHas('penghadap', function ($penghadap) use ($search) {
                            $penghadap->where(
                                'nama_penghadap',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })
            ->when($request->filled('tahun'), function ($query) use ($request) {
                $query->whereYear('tanggal', $request->input('tahun'));
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id_legalisasi');
    }

    private function getTahunList()
    {
        return Legalisasi::query()
            ->selectRaw('YEAR(tanggal) AS tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->filter()
            ->values();
    }

    private function validateData(Request $request): array
    {
        $request->merge([
            'no_urut' => is_string($request->input('no_urut'))
                ? trim($request->input('no_urut'))
                : $request->input('no_urut'),

            'sifat_surat' => is_string($request->input('sifat_surat'))
                ? trim($request->input('sifat_surat'))
                : $request->input('sifat_surat'),

            'nama_penghadap' => is_string($request->input('nama_penghadap'))
                ? trim($request->input('nama_penghadap'))
                : $request->input('nama_penghadap'),
        ]);

        return $request->validate([
            'no_urut' => ['required', 'string', 'max:30'],
            'tanggal' => ['required', 'date'],
            'sifat_surat' => ['required', 'string', 'max:150'],
            'nama_penghadap' => ['required', 'string', 'max:150'],
        ], [
            'no_urut.required' => 'Nomor urut wajib diisi.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'sifat_surat.required' => 'Sifat surat wajib diisi.',
            'nama_penghadap.required' => 'Nama penghadap wajib diisi.',
            'nama_penghadap.max' => 'Nama penghadap maksimal 150 karakter.',
        ]);
    }

    private function halamanData(Request $request, bool $modeKelola)
    {
        $this->adminOnly();

        $legalisasis = $this->filteredQuery($request)
            ->paginate(10)
            ->withQueryString();

        $legalisasiData = $legalisasis->getCollection()
            ->map(function ($item) {
                return [
                    'id_legalisasi' => $item->id_legalisasi,
                    'no_urut' => $item->no_urut,
                    'tanggal' => $item->tanggal?->format('Y-m-d'),
                    'sifat_surat' => $item->sifat_surat,
                    'nama_penghadap' => $item->penghadap?->nama_penghadap ?? '',
                ];
            })
            ->values();

        return view('admin.legalisasi.data_legalisasi', [
            'legalisasis' => $legalisasis,
            'legalisasiData' => $legalisasiData,
            'tahunList' => $this->getTahunList(),
            'modeKelola' => $modeKelola,
        ]);
    }

    public function data_legalisasi(Request $request)
    {
        return $this->halamanData($request, false);
    }

    public function kelola(Request $request)
    {
        return $this->halamanData($request, true);
    }

    public function kelolaKaryawan(Request $request)
    {
        $this->karyawanOnly();

        $legalisasis = $this->filteredQuery($request)
            ->paginate(10)
            ->withQueryString();

        $tahunList = $this->getTahunList();

        $legalisasiData = $legalisasis->getCollection()
            ->map(function ($item) {
                return [
                    'id_legalisasi' => $item->id_legalisasi,
                    'no_urut' => $item->no_urut,
                    'tanggal' => $item->tanggal?->format('Y-m-d'),
                    'sifat_surat' => $item->sifat_surat,
                    'nama_penghadap' => $item->penghadap?->nama_penghadap ?? '',
                ];
            })
            ->values();

        return view('karyawan.legalisasi.kelola_legalisasi', compact(
            'legalisasis',
            'tahunList',
            'legalisasiData'
        ));
    }

    public function storeKaryawan(Request $request)
    {
        $this->karyawanOnly();

        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated) {
            $legalisasi = Legalisasi::create([
                'no_urut' => $validated['no_urut'],
                'tanggal' => $validated['tanggal'],
                'sifat_surat' => $validated['sifat_surat'],
            ]);

            $legalisasi->penghadap()->create([
                'nama_penghadap' => $validated['nama_penghadap'],
            ]);
        });

        return redirect()
            ->route('karyawan.legalisasi.kelola')
            ->with('success', 'Data legalisasi berhasil ditambahkan.');
    }

    public function updateKaryawan(Request $request, Legalisasi $legalisasi)
    {
        $this->karyawanOnly();

        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated, $legalisasi) {
            $legalisasi->update([
                'no_urut' => $validated['no_urut'],
                'tanggal' => $validated['tanggal'],
                'sifat_surat' => $validated['sifat_surat'],
            ]);

            $legalisasi->penghadap()->updateOrCreate(
                [],
                ['nama_penghadap' => $validated['nama_penghadap']]
            );
        });

        return redirect()
            ->route('karyawan.legalisasi.kelola')
            ->with('success', 'Data legalisasi berhasil diperbarui.');
    }

    public function destroyKaryawan(Legalisasi $legalisasi)
    {
        $this->karyawanOnly();

        $legalisasi->delete();

        return redirect()
            ->route('karyawan.legalisasi.kelola')
            ->with('success', 'Data legalisasi berhasil dihapus.');
    }
}