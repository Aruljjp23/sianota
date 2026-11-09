@extends('layout.karyawan')

@section('title', 'Kelola SKMHT')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/kelola_skmht.css') }}">
@endpush

@section('content')

@if (session('success'))
    <div class="skmht-alert-success" id="skmhtSuccessMessage">
        <div class="skmht-alert-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <div class="skmht-alert-content">
            <strong>Berhasil!</strong>
            <span>{{ session('success') }}</span>
        </div>

        <button type="button"
            class="skmht-alert-close"
            onclick="this.closest('.skmht-alert-success').remove()"
            aria-label="Tutup pesan">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

<div class="skmht-page">

    <div class="skmht-heading">
        <div>
            <span class="page-label">Data Master</span>
            <h2>Kelola SKMHT</h2>
            <p>Kelola data Surat Kuasa Membebankan Hak Tanggungan.</p>
        </div>

        <button
            type="button"
            class="btn-add-skmht"
            data-bs-toggle="modal"
            data-bs-target="#tambahSkmhtModal"
        >
            <i class="fa-solid fa-plus"></i>
            <span>Tambah SKMHT</span>
        </button>
    </div>

    <form method="GET" action="{{ route('karyawan.skmht.kelola_skmht') }}" class="skmht-toolbar" id="filterSkmht">

        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="search"
                id="searchSkmht"
                value="{{ request('search') }}"
                placeholder="Cari nomor, jenis akta, atau nama penghadap..."
                autocomplete="off">
        </div>

        <div class="filter-box">
            <i class="fa-regular fa-calendar"></i>

            <select name="tahun" id="filterTahun">
                <option value="">Semua Tahun</option>

                @for ($tahun = date('Y'); $tahun >= 2020; $tahun--)
                    <option value="{{ $tahun }}"
                        @selected((string) request('tahun') === (string) $tahun)>
                        {{ $tahun }}
                    </option>
                @endfor
            </select>
        </div>

        @if (request()->hasAny(['search', 'tahun']))
            <a href="{{ route('karyawan.skmht.kelola_skmht') }}"
            class="btn-reset-filter">
                <i class="fa-solid fa-rotate-left"></i>
                <span>Reset</span>
            </a>
        @endif

    </form>


    <div class="skmht-list" id="skmhtList">

        @forelse ($skmhts as $index => $skmht)

            <article class="skmht-item">

                <div class="skmht-item-top">

                    <div class="skmht-number">
                        <span>SKMHT</span>
                        <strong>{{ $skmht->nomor_bulanan }}</strong>
                    </div>

                    <div class="skmht-actions">

                        <button
                            type="button"
                            class="action-btn detail"
                            title="Lihat detail"
                            data-bs-toggle="modal"
                            data-bs-target="#detailSkmhtModal"
                            data-id="{{ $skmht->id_skmht }}"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </button>

                        <button
                            type="button"
                            class="action-btn edit"
                            title="Edit data"
                            data-bs-toggle="modal"
                            data-bs-target="#editSkmhtModal"
                            data-id="{{ $skmht->id_skmht }}"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button
                            type="button"
                            class="action-btn delete"
                            title="Hapus data"
                            data-bs-toggle="modal"
                            data-bs-target="#hapusSkmhtModal"
                            data-id="{{ $skmht->id_skmht }}"
                        >
                            <i class="fa-regular fa-trash-can"></i>
                        </button>

                    </div>

                </div>


                <div class="skmht-item-info">

                    <div class="info-box">
                        <span>Tanggal Akta</span>

                        <strong>
                            {{ \Carbon\Carbon::parse($skmht->tanggal_akta)->format('d/m/Y') }}
                        </strong>

                        @if ($skmht->jam)
                            <small>
                                <i class="fa-regular fa-clock"></i>
                                {{ \Carbon\Carbon::parse($skmht->jam)->format('H:i') }}
                            </small>
                        @endif
                    </div>


                    <div class="info-box">
                        <span>Sifat / Jenis Akta</span>

                        <strong>
                            {{ $skmht->sifat_jenis_akta }}
                        </strong>
                    </div>


                    <div class="info-box address-info">
                        <span>Alamat</span>

                        <strong>
                            {{ $skmht->alamat ?: 'Belum ada alamat' }}
                        </strong>
                    </div>

                </div>


                <div class="skmht-item-bottom">

                    <div class="penghadap-title">
                        <div class="penghadap-icon">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>
                            <span>Nama Penghadap</span>
                            <strong>
                                {{ $skmht->penghadap->count() }}
                                Penghadap
                            </strong>
                        </div>
                    </div>

                    <div class="penghadap-list">

                        @forelse ($skmht->penghadap as $penghadap)

                            <span class="penghadap-name">
                                {{ $penghadap->nama_penghadap }}
                            </span>

                        @empty

                            <span class="empty-penghadap">
                                Belum ada nama penghadap
                            </span>

                        @endforelse

                    </div>

                </div>

            </article>

        @empty

            <div class="empty-state">

                <div class="empty-icon">
                    <i class="fa-regular fa-folder-open"></i>
                </div>

                <h3>Belum ada data SKMHT</h3>

                <p>
                    Data SKMHT yang ditambahkan akan muncul di halaman ini.
                </p>

            </div>

        @endforelse

    </div>


    <div
        class="filter-empty"
        id="filterEmpty"
        style="display: none;"
    >
        <div class="filter-empty-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>

        <strong>Data tidak ditemukan</strong>

        <span>
            Coba gunakan kata kunci atau tahun yang berbeda.
        </span>
    </div>


    @if ($skmhts->hasPages())

        <div class="pagination-wrapper">

            <div class="pagination-info">
                Menampilkan
                <strong>{{ $skmhts->firstItem() }}</strong>
                -
                <strong>{{ $skmhts->lastItem() }}</strong>
                dari
                <strong>{{ $skmhts->total() }}</strong>
                data
            </div>

            <div class="pagination-links">
                {{ $skmhts->links() }}
            </div>

        </div>

    @endif

</div>


<div
    class="modal fade"
    id="tambahSkmhtModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content skmht-modal">

            <div class="modal-header">

                <div>
                    <span class="modal-label">Data SKMHT</span>
                    <h5 class="modal-title">Tambah SKMHT</h5>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <form
                action="{{ route('karyawan.skmht.store') }}"
                method="POST"
            >
                @csrf

                <div class="modal-body">

                    <div class="form-grid">

                        <div class="form-group">
                            <label>Nomor Bulanan</label>

                            <input
                                type="text"
                                name="nomor_bulanan"
                                class="form-control"
                                placeholder="Contoh: 01/2026"
                                maxlength="20"
                                required
                            >
                        </div>


                        <div class="form-group">
                            <label>Tanggal Akta</label>

                            <input
                                type="date"
                                name="tanggal_akta"
                                class="form-control"
                                required
                            >
                        </div>


                        <div class="form-group">
                            <label>Sifat / Jenis Akta</label>

                            <input
                                type="text"
                                name="sifat_jenis_akta"
                                class="form-control"
                                placeholder="Masukkan sifat atau jenis akta"
                                maxlength="100"
                                required
                            >
                        </div>


                        <div class="form-group">
                            <label>Jam</label>

                            <input
                                type="time"
                                name="jam"
                                class="form-control"
                            >
                        </div>

                    </div>


                    <div class="form-group modal-full">
                        <label>Alamat</label>

                        <textarea
                            name="alamat"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan alamat"
                        ></textarea>
                    </div>


                    <div class="penghadap-form">

                        <div class="penghadap-heading">

                            <div>
                                <strong>Nama Penghadap</strong>
                                <span>
                                    Tambahkan satu atau beberapa nama penghadap.
                                </span>
                            </div>

                            <button
                                type="button"
                                class="btn-add-penghadap"
                                id="tambahPenghadap"
                            >
                                <i class="fa-solid fa-plus"></i>
                                Tambah Nama
                            </button>

                        </div>


                        <div id="penghadapContainer">

                            <div class="penghadap-input">

                                <span class="penghadap-number">1</span>

                                <input
                                    type="text"
                                    name="nama_penghadap[]"
                                    class="form-control"
                                    placeholder="Nama penghadap"
                                    maxlength="150"
                                    required
                                >

                                <button
                                    type="button"
                                    class="remove-penghadap"
                                    disabled
                                >
                                    <i class="fa-solid fa-xmark"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn-cancel"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn-save"
                    >
                        <i class="fa-solid fa-check"></i>
                        Simpan SKMHT
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


<div
    class="modal fade"
    id="detailSkmhtModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content skmht-modal">

            <div class="modal-header">

                <div>
                    <span class="modal-label">Informasi SKMHT</span>
                    <h5 class="modal-title">Detail SKMHT</h5>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="detail-number">
                    <span>Nomor Bulanan</span>
                    <strong id="detailNomorBulanan">-</strong>
                </div>


                <div class="detail-grid">

                    <div class="detail-item">
                        <span>Tanggal Akta</span>
                        <strong id="detailTanggalAkta">-</strong>
                    </div>

                    <div class="detail-item">
                        <span>Jam</span>
                        <strong id="detailJam">-</strong>
                    </div>

                    <div class="detail-item">
                        <span>Sifat / Jenis Akta</span>
                        <strong id="detailSifatJenis">-</strong>
                    </div>

                </div>


                <div class="detail-address">
                    <span>Alamat</span>
                    <p id="detailAlamat">-</p>
                </div>


                <div class="detail-penghadap">

                    <div class="detail-section-title">
                        <span>Nama Penghadap</span>
                        <strong id="detailJumlahPenghadap">0 Penghadap</strong>
                    </div>

                    <div id="detailPenghadap"></div>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-cancel"
                    data-bs-dismiss="modal"
                >
                    Kembali
                </button>

            </div>

        </div>
    </div>
</div>


<div
    class="modal fade"
    id="editSkmhtModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content skmht-modal">

            <div class="modal-header">

                <div>
                    <span class="modal-label">Perubahan Data</span>
                    <h5 class="modal-title">Edit SKMHT</h5>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <form
                id="formEditSkmht"
                method="POST"
                data-url="{{ route('karyawan.skmht.update', ':id') }}"
            >
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="form-grid">

                        <div class="form-group">
                            <label>Nomor Bulanan</label>

                            <input
                                type="text"
                                name="nomor_bulanan"
                                id="editNomorBulanan"
                                class="form-control"
                                maxlength="20"
                                required
                            >
                        </div>


                        <div class="form-group">
                            <label>Tanggal Akta</label>

                            <input
                                type="date"
                                name="tanggal_akta"
                                id="editTanggalAkta"
                                class="form-control"
                                required
                            >
                        </div>


                        <div class="form-group">
                            <label>Sifat / Jenis Akta</label>

                            <input
                                type="text"
                                name="sifat_jenis_akta"
                                id="editSifatJenis"
                                class="form-control"
                                maxlength="100"
                                required
                            >
                        </div>


                        <div class="form-group">
                            <label>Jam</label>

                            <input
                                type="time"
                                name="jam"
                                id="editJam"
                                class="form-control"
                            >
                        </div>

                    </div>


                    <div class="form-group modal-full">
                        <label>Alamat</label>

                        <textarea
                            name="alamat"
                            id="editAlamat"
                            class="form-control"
                            rows="3"
                        ></textarea>
                    </div>


                    <div class="penghadap-form">

                        <div class="penghadap-heading">

                            <div>
                                <strong>Nama Penghadap</strong>
                                <span>
                                    Perbarui nama penghadap jika diperlukan.
                                </span>
                            </div>

                            <button
                                type="button"
                                class="btn-add-penghadap"
                                id="tambahPenghadapEdit"
                            >
                                <i class="fa-solid fa-plus"></i>
                                Tambah Nama
                            </button>

                        </div>


                        <div id="penghadapEditContainer"></div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn-cancel"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn-save"
                    >
                        <i class="fa-solid fa-check"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


<div
    class="modal fade"
    id="hapusSkmhtModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content delete-modal">

            <div class="delete-icon">
                <i class="fa-regular fa-trash-can"></i>
            </div>

            <h5>Hapus SKMHT?</h5>

            <p>
                Data SKMHT dan seluruh nama penghadap yang terkait akan dihapus.
            </p>


            <form
                id="formHapusSkmht"
                method="POST"
                data-url="{{ route('karyawan.skmht.destroy', ':id') }}"
            >
                @csrf
                @method('DELETE')

                <div class="delete-actions">

                    <button
                        type="button"
                        class="btn-cancel"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn-delete"
                    >
                        Hapus
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection


@php
    $skmhtData = $skmhts->getCollection()->map(function ($item) {
        return [
            'id_skmht' => $item->id_skmht,
            'nomor_bulanan' => $item->nomor_bulanan,
            'tanggal_akta' => $item->tanggal_akta
                ? \Carbon\Carbon::parse($item->tanggal_akta)->format('Y-m-d')
                : null,
            'tanggal_tampil' => $item->tanggal_akta
                ? \Carbon\Carbon::parse($item->tanggal_akta)->format('d/m/Y')
                : '-',
            'sifat_jenis_akta' => $item->sifat_jenis_akta,
            'alamat' => $item->alamat,
            'jam' => $item->jam
                ? \Carbon\Carbon::parse($item->jam)->format('H:i')
                : '',
            'penghadap' => $item->penghadap
                ->pluck('nama_penghadap')
                ->values()
                ->all(),
        ];
    })->values()->all();
@endphp

@push('scripts')
    <script>
        window.skmhtData = @json($skmhtData);
        window.skmhtShowUrl = "{{ route('karyawan.skmht.show', ':id') }}";
    </script>

    <script src="{{ asset('js/kelola_skmht.js') }}"></script>
@endpush