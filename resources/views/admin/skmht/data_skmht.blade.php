@extends('layout.admin')

@section('title', 'Data SKMHT')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/skmht.css') }}">
@endpush

@section('content')

<div class="skmht-page">

    <div class="skmht-heading">
        <div>
            <h2>Data SKMHT</h2>
            <p>Data dokumen SKMHT yang dikelola oleh karyawan.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="skmht-alert skmht-alert-success">
            <i class="fa-solid fa-circle-check"></i>

            <span>{{ session('success') }}</span>

            <button
                type="button"
                class="alert-close"
                aria-label="Tutup"
                onclick="this.parentElement.remove()"
            >
                &times;
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="skmht-alert skmht-alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>

            <div>
                <strong>Data belum bisa diproses.</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="skmht-stats">

        <div class="skmht-stat-card">
            <div class="skmht-stat-icon green">
                <i class="fa-solid fa-file-signature"></i>
            </div>

            <div>
                <span>Total Dokumen</span>
                <strong>
                    {{ number_format($totalSkmht, 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <div class="skmht-stat-card">
            <div class="skmht-stat-icon purple">
                <i class="fa-solid fa-calendar-days"></i>
            </div>

            <div>
                <span>Akta Bulan Ini</span>
                <strong>
                    {{ number_format($totalBulan, 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <div class="skmht-stat-card">
            <div class="skmht-stat-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>

            <div>
                <span>Total Penghadap</span>
                <strong>
                    {{ number_format($totalPenghadap, 0, ',', '.') }}
                </strong>
            </div>
        </div>

    </div>

    <div class="skmht-table-card">

        <div class="skmht-table-header">
            <div>
                <h3>Daftar Dokumen SKMHT</h3>
                <p>Data SKMHT yang tersimpan dalam sistem.</p>
            </div>
        </div>

        <form
            action="{{ route('skmht.data_skmht') }}"
            method="GET"
            class="skmht-filter-form"
        >

            <div class="skmht-search">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor, jenis akta, alamat, atau penghadap..."
                >
            </div>

            <select
                name="tahun"
                class="skmht-filter-select"
            >
                <option value="">Semua tahun</option>

                @foreach ($tahunAkta as $tahun)
                    <option
                        value="{{ $tahun }}"
                        @selected((string) request('tahun') === (string) $tahun)
                    >
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>

            <input
                type="date"
                name="tanggal_dari"
                class="skmht-filter-date"
                value="{{ request('tanggal_dari') }}"
                aria-label="Tanggal akta dari"
                title="Tanggal akta dari"
            >

            <input
                type="date"
                name="tanggal_sampai"
                class="skmht-filter-date"
                value="{{ request('tanggal_sampai') }}"
                aria-label="Tanggal akta sampai"
                title="Tanggal akta sampai"
            >

            <button
                type="submit"
                class="btn-skmht-search"
            >
                Cari
            </button>

            @if (request()->hasAny([
                'search',
                'tahun',
                'tanggal_dari',
                'tanggal_sampai'
            ]))
                <a
                    href="{{ route('skmht.data_skmht') }}"
                    class="btn-skmht-reset"
                    title="Reset filter"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif

        </form>

        <div class="table-responsive">

            <table class="table skmht-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Urut/Bulanan</th>
                        <th>Tanggal Akta</th>
                        <th>Sifat/Jenis Akta</th>
                        <th>Alamat</th>
                        <th>Nama Penghadap</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($skmhts as $item)

                        <tr>

                            <td>
                                {{ $skmhts->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="skmht-number">
                                    {{ $item->nomor_bulanan }}
                                </span>
                            </td>

                            <td>
                                {{ $item->tanggal_akta->format('d M Y') }}
                            </td>

                            <td>
                                <span class="skmht-type">
                                    {{ $item->sifat_jenis_akta }}
                                </span>
                            </td>

                            <td>
                                <div class="skmht-address">
                                    {{ $item->alamat ?: '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="skmht-names">

                                    @forelse ($item->penghadap as $penghadap)

                                        <span>
                                            {{ $penghadap->nama_penghadap }}
                                        </span>

                                    @empty

                                        <span>-</span>

                                    @endforelse

                                </div>
                            </td>

                            <td class="text-center">
                                <div class="skmht-actions">
                                    <button
                                        type="button"
                                        class="action-btn detail-skmht"
                                        data-id="{{ $item->id_skmht }}"
                                        title="Lihat detail SKMHT"
                                        aria-label="Lihat detail SKMHT"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7">

                                <div class="skmht-empty">

                                    <i class="fa-regular fa-folder-open"></i>

                                    <strong>
                                        Belum ada data SKMHT
                                    </strong>

                                    <span>
                                        Data SKMHT yang dimasukkan karyawan akan tampil di sini.
                                    </span>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($skmhts->hasPages())

            <div class="skmht-pagination">

                <div class="skmht-pagination-info">

                    Menampilkan

                    <strong>
                        {{ $skmhts->firstItem() }}
                    </strong>

                    sampai

                    <strong>
                        {{ $skmhts->lastItem() }}
                    </strong>

                    dari

                    <strong>
                        {{ $skmhts->total() }}
                    </strong>

                    data

                </div>

                <div class="skmht-pagination-links">

                    @if ($skmhts->onFirstPage())

                        <span class="skmht-page-button disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>

                    @else

                        <a
                            href="{{ $skmhts->previousPageUrl() }}"
                            class="skmht-page-button"
                        >
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>

                    @endif

                    @foreach (
                        $skmhts->getUrlRange(
                            max(1, $skmhts->currentPage() - 1),
                            min($skmhts->lastPage(), $skmhts->currentPage() + 1)
                        ) as $page => $url
                    )

                        @if ($page == $skmhts->currentPage())

                            <span class="skmht-page-button active">
                                {{ $page }}
                            </span>

                        @else

                            <a
                                href="{{ $url }}"
                                class="skmht-page-button"
                            >
                                {{ $page }}
                            </a>

                        @endif

                    @endforeach

                    @if ($skmhts->hasMorePages())

                        <a
                            href="{{ $skmhts->nextPageUrl() }}"
                            class="skmht-page-button"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                    @else

                        <span class="skmht-page-button disabled">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>

<div
    class="modal fade"
    id="detailSkmhtModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content skmht-modal">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title">
                        Detail Dokumen SKMHT
                    </h5>

                    <p>
                        Informasi lengkap dokumen yang dipilih.
                    </p>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>

            <div class="modal-body">

                <div class="skmht-detail-grid">

                    <div>
                        <span>Nomor urut/bulanan</span>
                        <strong id="detailNomor">-</strong>
                    </div>

                    <div>
                        <span>Tanggal akta</span>
                        <strong id="detailTanggal">-</strong>
                    </div>

                    <div>
                        <span>Sifat/jenis akta</span>
                        <strong id="detailSifat">-</strong>
                    </div>

                    <div>
                        <span>Alamat</span>
                        <strong id="detailAlamat">-</strong>
                    </div>

                    <div class="detail-full">

                        <span>Nama penghadap</span>

                        <div
                            id="detailNama"
                            class="skmht-detail-names"
                        ></div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-skmht-cancel"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>
</div>

<script>
    window.skmhtData = {{ Illuminate\Support\Js::from(
        $skmhts->getCollection()->map(function ($item) {
            return [
                'id_skmht' => $item->id_skmht,
                'nomor_bulanan' => $item->nomor_bulanan,
                'tanggal_akta' => $item->tanggal_akta->format('Y-m-d'),
                'tanggal_format' => $item->tanggal_akta->format('d M Y'),
                'sifat_jenis_akta' => $item->sifat_jenis_akta,
                'alamat' => $item->alamat,
                'nama_penghadap' => $item->penghadap?->nama_penghadap ?? '-',
            ];
        })->values()->all()
    ) }};
</script>

@endsection

@push('scripts')
    <script src="{{ asset('js/skmht.js') }}"></script>
@endpush