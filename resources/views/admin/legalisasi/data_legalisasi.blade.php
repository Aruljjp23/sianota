@extends('layout.admin')

@section('title', 'Data Legalisasi')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/legalisasi.css') }}">
@endpush

@section('content')

<div class="legalisasi-page">

    <div class="legalisasi-heading">
        <div>
            <h1>Data Legalisasi</h1>
            <p>Data dokumen legalisasi yang dikelola oleh karyawan.</p>
        </div>
    </div>

    <div class="legalisasi-summary">
        <div class="legalisasi-summary-icon">
            <i class="fa-solid fa-stamp"></i>
        </div>

        <div>
            <span>Total Data Legalisasi</span>
            <h3>{{ $legalisasis->total() }}</h3>
        </div>
    </div>

    <section class="legalisasi-card">

        <div class="legalisasi-card-header">
            <div>
                <h2>Daftar Legalisasi</h2>
            </div>
        </div>

        <form method="GET"
            action="{{ route('legalisasi.data_legalisasi') }}"
            class="legalisasi-filter"
            id="legalisasiFilter">

            <div class="legalisasi-search">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text"
                    name="search"
                    id="legalisasiSearch"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor, sifat surat, nama penghadap..."
                    autocomplete="off">
            </div>

            <select name="tahun"
                id="legalisasiTahun"
                aria-label="Filter tahun">

                <option value="">Semua Tahun</option>

                @foreach ($tahunList as $tahun)
                    <option value="{{ $tahun }}"
                        @selected((string) request('tahun') === (string) $tahun)>
                        {{ $tahun }}
                    </option>
                @endforeach

            </select>

            @if (request()->hasAny(['search', 'tahun']))
                <a href="{{ route('legalisasi.data_legalisasi') }}"
                    class="btn-legalisasi-reset"
                    title="Reset pencarian">

                    <i class="fa-solid fa-rotate-left"></i>

                </a>
            @endif

        </form>

        <div class="table-responsive">

            <table class="table legalisasi-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Urut</th>
                        <th>Tanggal</th>
                        <th>Sifat Surat</th>
                        <th>Nama Penghadap</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($legalisasis as $item)

                        <tr>

                            <td>
                                {{ $legalisasis->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="legalisasi-number">
                                    {{ $item->no_urut }}
                                </span>
                            </td>

                            <td>
                                {{ $item->tanggal?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $item->sifat_surat }}
                            </td>

                            <td>
                                <div class="legalisasi-names">
                                    <span>
                                        {{ $item->penghadap?->nama_penghadap ?? 'Belum ada nama' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <div class="legalisasi-actions">

                                    <button type="button"
                                        class="btn-legalisasi-detail btn-detail-legalisasi"
                                        data-id="{{ $item->id_legalisasi }}"
                                        title="Lihat detail"
                                        aria-label="Lihat detail">

                                        <i class="fa-solid fa-eye"></i>

                                    </button>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="legalisasi-empty">

                                <i class="fa-regular fa-folder-open"></i>

                                <strong>
                                    Data tidak ditemukan
                                </strong>

                                <span>
                                    Belum ada data yang sesuai dengan pencarian.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($legalisasis->hasPages())

            <div class="legalisasi-pagination">

                <div class="legalisasi-pagination-info">
                    Menampilkan
                    <strong>{{ $legalisasis->firstItem() }}</strong>
                    sampai
                    <strong>{{ $legalisasis->lastItem() }}</strong>
                    dari
                    <strong>{{ $legalisasis->total() }}</strong>
                    data
                </div>

                <div class="legalisasi-pagination-links">

                    @if ($legalisasis->onFirstPage())
                        <span class="legalisasi-page-button disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $legalisasis->previousPageUrl() }}"
                            class="legalisasi-page-button">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach ($legalisasis->getUrlRange(
                        max(1, $legalisasis->currentPage() - 1),
                        min($legalisasis->lastPage(), $legalisasis->currentPage() + 1)
                    ) as $page => $url)

                        @if ($page == $legalisasis->currentPage())
                            <span class="legalisasi-page-button active">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="legalisasi-page-button">
                                {{ $page }}
                            </a>
                        @endif

                    @endforeach

                    @if ($legalisasis->hasMorePages())
                        <a href="{{ $legalisasis->nextPageUrl() }}"
                            class="legalisasi-page-button">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="legalisasi-page-button disabled">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif

                </div>

            </div>

        @endif

    </section>

</div>


<div class="modal fade"
    id="detailLegalisasiModal"
    tabindex="-1"
    aria-labelledby="detailLegalisasiModalTitle"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered legalisasi-detail-dialog">

        <div class="modal-content legalisasi-detail-modal">

            <div class="modal-header">

                <div>
                    <h5 class="modal-title"
                        id="detailLegalisasiModalTitle">
                        Detail Legalisasi
                    </h5>

                    <p class="mb-0">
                        Informasi lengkap dokumen legalisasi.
                    </p>
                </div>

                <button type="button"
                    class="legalisasi-back"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="legalisasi-detail-grid">

                    <div class="legalisasi-detail-item">
                        <span>Nomor Urut</span>
                        <strong id="detailNoUrutLegalisasi">-</strong>
                    </div>

                    <div class="legalisasi-detail-item">
                        <span>Tanggal</span>
                        <strong id="detailTanggalLegalisasi">-</strong>
                    </div>

                    <div class="legalisasi-detail-item legalisasi-detail-full">
                        <span>Sifat Surat</span>
                        <strong id="detailSifatSuratLegalisasi">-</strong>
                    </div>

                    <div class="legalisasi-detail-item legalisasi-detail-full">
                        <span>Nama Penghadap</span>

                        <div class="legalisasi-detail-names"
                            id="detailNamaPenghadapLegalisasi">
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@php
    $legalisasiDataAdmin = $legalisasis->getCollection()->map(function ($item) {
        return [
            'id_legalisasi' => $item->id_legalisasi,
            'no_urut' => $item->no_urut,
            'tanggal' => $item->tanggal?->format('Y-m-d'),
            'tanggal_format' => $item->tanggal?->format('d/m/Y'),
            'sifat_surat' => $item->sifat_surat,
            'nama_penghadap' => $item->penghadap?->nama_penghadap ?? '',
        ];
    })->values()->all();
@endphp


@push('scripts')

<script>
    window.legalisasiData = @json($legalisasiDataAdmin);
</script>

<script src="{{ asset('js/legalisasi.js') }}"></script>

@endpush

@endsection