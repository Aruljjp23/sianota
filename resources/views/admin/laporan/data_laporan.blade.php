@extends('layout.admin')

@section('title', 'Laporan')
@section('heading', 'Laporan')
@section('subtitle', 'Filter dan cetak laporan dokumen notaris')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@section('content')

<div class="report-page">

    <div class="report-heading">
        <div>
            <h2>Laporan Dokumen</h2>
            {{-- <p>Pilih jenis dokumen dan gunakan filter untuk menampilkan laporan.</p> --}}
        </div>

        <a href="{{ route('laporan.pdf', request()->query()) }}"
           class="btn btn-success report-print">
            <i class="fa-solid fa-file-pdf me-2"></i>
            Cetak PDF
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="report-card">

        <form action="{{ route('laporan.data_laporan') }}" method="GET" class="report-filter" id="reportFilter">

            <div class="filter-field">
                <label for="jenis">Jenis Laporan</label>
                <select name="jenis" id="jenis" class="form-select">
                    <option value="skmht" {{ $jenis === 'skmht' ? 'selected' : '' }}>
                        Laporan SKMHT
                    </option>
                    <option value="legalisasi" {{ $jenis === 'legalisasi' ? 'selected' : '' }}>
                        Laporan Legalisasi
                    </option>
                </select>
            </div>

            <div class="filter-field filter-search">
                <label for="search">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Nomor, jenis akta, atau nama penghadap"
                >
            </div>

            <div class="filter-field">
                <label for="sifat">Sifat/Jenis Akta</label>
                <select name="sifat" id="sifat" class="form-select">
                    <option value="">Semua jenis</option>
                    @foreach ($daftarSifat as $sifat)
                        <option value="{{ $sifat }}"
                            {{ request('sifat') === $sifat ? 'selected' : '' }}>
                            {{ $sifat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field">
                <label for="tahun">Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    <option value="">Semua tahun</option>
                    @foreach ($daftarTahun as $tahun)
                        <option value="{{ $tahun }}"
                            {{ (string) request('tahun') === (string) $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input
                    type="date"
                    name="tanggal_mulai"
                    id="tanggal_mulai"
                    class="form-control"
                    value="{{ request('tanggal_mulai') }}"
                >
            </div>

            <div class="filter-field">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input
                    type="date"
                    name="tanggal_selesai"
                    id="tanggal_selesai"
                    class="form-control"
                    value="{{ request('tanggal_selesai') }}"
                >
            </div>

            <div class="report-filter-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Terapkan Filter
                </button>

                <a href="{{ route('laporan.data_laporan', ['jenis' => $jenis]) }}" class="btn btn-light">
                    Reset
                </a>
            </div>

        </form>

        <div class="report-table-heading">
            <div>
                <h3>
                    {{ $jenis === 'skmht' ? 'Laporan SKMHT' : 'Laporan Legalisasi' }}
                </h3>
                <p>Data yang sesuai dengan filter yang dipilih.</p>
            </div>

            <span class="report-total">
                {{ $laporan->total() }} dokumen
            </span>
        </div>

        <div class="table-responsive">
            <table class="table report-table align-middle">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th>Nomor Urut/Bulanan</th>
                        <th>Tanggal Akta</th>
                        <th>Sifat/Jenis Akta</th>

                        @if ($jenis === 'skmht')
                            <th>Alamat</th>
                        @endif

                        <th>Nama Penghadap</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($laporan as $index => $item)
                        <tr>
                            <td class="text-center">
                                {{ $laporan->firstItem() + $index }}
                            </td>
                            <td>{{ $item['nomor'] }}</td>
                            <td>{{ $item['tanggal'] }}</td>
                            <td>{{ $item['sifat'] }}</td>

                            @if ($jenis === 'skmht')
                                <td>{{ $item['alamat'] }}</td>
                            @endif

                            <td>
                                <span class="report-name">
                                    {{ $item['penghadap'] ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $jenis === 'skmht' ? 6 : 5 }}"
                                class="report-empty">
                                <i class="fa-regular fa-folder-open"></i>
                                <strong>Data tidak ditemukan</strong>
                                <span>Coba ubah filter atau kata kunci pencarian.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($laporan->hasPages())
            <div class="report-pagination">

                <div class="report-pagination-info">
                    Menampilkan
                    <strong>{{ $laporan->firstItem() }}</strong>
                    sampai
                    <strong>{{ $laporan->lastItem() }}</strong>
                    dari
                    <strong>{{ $laporan->total() }}</strong>
                    dokumen
                </div>

                <div class="report-pagination-links">

                    @if ($laporan->onFirstPage())
                        <span class="report-page-button disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $laporan->previousPageUrl() }}"
                        class="report-page-button">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach (
                        $laporan->getUrlRange(
                            max(1, $laporan->currentPage() - 1),
                            min($laporan->lastPage(), $laporan->currentPage() + 1)
                        ) as $page => $url
                    )
                        @if ($page == $laporan->currentPage())
                            <span class="report-page-button active">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                            class="report-page-button">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($laporan->hasMorePages())
                        <a href="{{ $laporan->nextPageUrl() }}"
                        class="report-page-button">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="report-page-button disabled">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif

                </div>

            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('reportFilter');

        if (!form) return;

        const search = form.querySelector('[name="search"]');
        const autoFilters = ['jenis', 'sifat', 'tahun', 'tanggal_mulai', 'tanggal_selesai'];

        let searchTimer;

        autoFilters.forEach(function (name) {
            const field = form.querySelector(`[name="${name}"]`);

            if (field) {
                field.addEventListener('change', function () {
                    form.requestSubmit();
                });
            }
        });

        if (search) {
            search.addEventListener('input', function () {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(function () {
                    form.requestSubmit();
                }, 500);
            });
        }
    });
</script>

@endsection