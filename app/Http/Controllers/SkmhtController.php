<?php

namespace App\Http\Controllers;

use App\Models\Skmht;
use App\Models\Penghadap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SkmhtController extends Controller
{
    private function adminOnly(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'admin',
            403
        );
    }

    private function filteredQuery(Request $request): Builder
    {
        return Skmht::with('penghadap')
            ->when($request->filled('search'), function ($query) use ($request) {
                $keyword = trim($request->input('search'));

                $query->where(function ($query) use ($keyword) {
                    $query->where('nomor_bulanan', 'like', "%{$keyword}%")
                        ->orWhere('sifat_jenis_akta', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%")
                        ->orWhereHas('penghadap', function ($query) use ($keyword) {
                            $query->where('nama_penghadap', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($request->filled('sifat'), function ($query) use ($request) {
                $query->where('sifat_jenis_akta', $request->input('sifat'));
            })
            ->when(
                $request->filled('tahun')
                    && preg_match('/^\d{4}$/', (string) $request->input('tahun')),
                function ($query) use ($request) {
                    $query->whereYear('tanggal_akta', (int) $request->input('tahun'));
                }
            )
            ->when($request->filled('tanggal_dari'), function ($query) use ($request) {
                $tanggalDari = $request->input('tanggal_dari');

                if (strtotime($tanggalDari) !== false) {
                    $query->whereDate('tanggal_akta', '>=', $tanggalDari);
                }
            })
            ->when($request->filled('tanggal_sampai'), function ($query) use ($request) {
                $tanggalSampai = $request->input('tanggal_sampai');

                if (strtotime($tanggalSampai) !== false) {
                    $query->whereDate('tanggal_akta', '<=', $tanggalSampai);
                }
            });
    }

    public function data_skmht(Request $request)
    {
        $this->adminOnly();

        $skmhts = $this->filteredQuery($request)
            ->orderByDesc('tanggal_akta')
            ->orderByDesc('id_skmht')
            ->paginate(10)
            ->withQueryString();

        $totalSkmht = Skmht::count();

        $totalBulan = Skmht::whereYear('tanggal_akta', now()->year)
            ->whereMonth('tanggal_akta', now()->month)
            ->count();

        $totalPenghadap = Penghadap::whereNotNull('id_skmht')->count();

        $jenisAkta = Skmht::query()
            ->select('sifat_jenis_akta')
            ->distinct()
            ->orderBy('sifat_jenis_akta')
            ->pluck('sifat_jenis_akta');

        $tahunAkta = Skmht::query()
            ->selectRaw('YEAR(tanggal_akta) AS tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('admin.skmht.data_skmht', compact(
            'skmhts',
            'totalSkmht',
            'totalBulan',
            'totalPenghadap',
            'jenisAkta',
            'tahunAkta'
        ));
    }

    private function validateData(Request $request): array
    {
        $namaPenghadap = $request->input('nama_penghadap', []);

        if (!is_array($namaPenghadap)) {
            $namaPenghadap = [];
        }

        $request->merge([
            'nomor_bulanan' => trim($request->input('nomor_bulanan', '')),
            'sifat_jenis_akta' => trim($request->input('sifat_jenis_akta', '')),
            'nama_penghadap' => $this->cleanNamaPenghadap($namaPenghadap)->all(),
        ]);

        return $request->validate([
            'nomor_bulanan' => 'required|string|max:20',
            'tanggal_akta' => 'required|date',
            'sifat_jenis_akta' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'jam' => 'nullable|date_format:H:i',
            'nama_penghadap' => 'required|array|min:1',
            'nama_penghadap.*' => 'required|string|max:150',
        ], [
            'nomor_bulanan.required' => 'Nomor urut/bulanan wajib diisi.',
            'tanggal_akta.required' => 'Tanggal akta wajib diisi.',
            'sifat_jenis_akta.required' => 'Sifat/jenis akta wajib diisi.',
            'nama_penghadap.required' => 'Nama penghadap wajib diisi.',
            'nama_penghadap.array' => 'Format nama penghadap tidak valid.',
            'nama_penghadap.min' => 'Minimal harus ada satu nama penghadap.',
            'nama_penghadap.*.required' => 'Nama penghadap tidak boleh kosong.',
            'nama_penghadap.*.max' => 'Nama penghadap maksimal 150 karakter.',
        ]);
    }

    private function cleanNamaPenghadap(array $nama)
    {
        return collect($nama)
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '')
            ->unique()
            ->values();
    }

    public function destroy(Skmht $skmht)
    {
        $this->adminOnly();

        $skmht->delete();

        return redirect()->route('skmht.data_skmht')->with('success', 'Data SKMHT berhasil dihapus.');
    }

    private function karyawanOnly(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'karyawan',
            403
        );
    }

    public function kelola_skmht(Request $request)
    {
        $this->karyawanOnly();

        $skmhts = $this->filteredQuery($request)
            ->orderByDesc('tanggal_akta')
            ->orderByDesc('id_skmht')
            ->paginate(10)
            ->withQueryString();

        return view('karyawan.skmht.kelola_skmht', compact('skmhts'));
    }

    public function showKaryawan(Skmht $skmht)
    {
        $this->karyawanOnly();

        $skmht->load('penghadap');

        return response()->json($skmht);
    }

    public function storeKaryawan(Request $request)
    {
        $this->karyawanOnly();

        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated) {
            $skmht = Skmht::create([
                'nomor_bulanan' => $validated['nomor_bulanan'],
                'tanggal_akta' => $validated['tanggal_akta'],
                'sifat_jenis_akta' => $validated['sifat_jenis_akta'],
                'alamat' => $validated['alamat'] ?? null,
                'jam' => $validated['jam'] ?? null,
            ]);

            $skmht->penghadap()->createMany(
                collect($validated['nama_penghadap'])
                    ->map(fn ($nama) => [
                        'nama_penghadap' => $nama,
                    ])
                    ->all()
            );
        });

        return redirect()
            ->route('karyawan.skmht.kelola_skmht')
            ->with('success', 'Data SKMHT berhasil ditambahkan.');
    }

    public function updateKaryawan(Request $request, Skmht $skmht)
    {
        $this->karyawanOnly();

        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated, $skmht) {
            $skmht->update([
                'nomor_bulanan' => $validated['nomor_bulanan'],
                'tanggal_akta' => $validated['tanggal_akta'],
                'sifat_jenis_akta' => $validated['sifat_jenis_akta'],
                'alamat' => $validated['alamat'] ?? null,
                'jam' => $validated['jam'] ?? null,
            ]);

            $skmht->penghadap()->delete();

            $skmht->penghadap()->createMany(
                collect($validated['nama_penghadap'])
                    ->map(fn ($nama) => [
                        'nama_penghadap' => $nama,
                    ])
                    ->all()
            );
        });

        return redirect()
            ->route('karyawan.skmht.kelola_skmht')
            ->with('success', 'Data SKMHT berhasil diperbarui.');
    }

    public function destroyKaryawan(Skmht $skmht)
    {
        $this->karyawanOnly();

        $skmht->delete();

        return redirect()->route('karyawan.skmht.kelola_skmht')->with('success', 'Data SKMHT berhasil dihapus.');
    }
}