@extends('layout.karyawan')

@section('title', 'Kelola Legalisasi')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/kelola_legalisasi.css') }}">
@endpush

@section('content')
<div class="legalisasi-page">

    <div class="legalisasi-heading">
        <div>
            <h1>Kelola Legalisasi</h1>
            <p>Kelola data dokumen legalisasi dengan mudah.</p>
        </div>

        <button type="button"
            class="btn-legalisasi-primary"
            id="btnTambahLegalisasi"
            data-bs-toggle="modal"
            data-bs-target="#legalisasiModal">
            <i class="fa-solid fa-plus"></i>
            Tambah Legalisasi
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('success') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Periksa kembali data yang dimasukkan.</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup"></button>
        </div>
    @endif

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
                <p>Data dokumen yang tersimpan dalam sistem.</p>
            </div>
        </div>

        <form method="GET"
            action="{{ route('karyawan.legalisasi.kelola') }}"
            class="legalisasi-filter"
            id="legalisasiFilter">

            <div class="legalisasi-search">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text"
                    name="search"
                    id="legalisasiSearch"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor, sifat surat, atau nama..."
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
                <a href="{{ route('karyawan.legalisasi.kelola') }}"
                    class="btn-legalisasi-reset"
                    title="Reset filter">
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

                            <td>{{ $item->sifat_surat }}</td>

                            <td>
                                <div class="legalisasi-names">
                                    @forelse ($item->penghadap as $penghadap)
                                        <span>{{ $penghadap->nama_penghadap }}</span>
                                    @empty
                                        <span class="text-muted">Belum ada nama penghadap</span>
                                    @endforelse
                                </div>
                            </td>

                            <td>
                                <div class="legalisasi-actions">
                                    <button type="button"
                                        class="btn-legalisasi-edit btn-edit-legalisasi"
                                        data-id="{{ $item->id_legalisasi }}"
                                        title="Edit data"
                                        aria-label="Edit legalisasi">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button type="button"
                                        class="btn-legalisasi-delete btn-hapus-legalisasi"
                                        data-id="{{ $item->id_legalisasi }}"
                                        data-nomor="{{ $item->no_urut }}"
                                        title="Hapus data"
                                        aria-label="Hapus legalisasi">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="legalisasi-empty">
                                <i class="fa-regular fa-folder-open"></i>
                                <strong>Data legalisasi belum tersedia</strong>
                                <span>
                                    Coba ubah pencarian atau tambahkan data legalisasi baru.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($legalisasis->hasPages())
            <div class="legalisasi-pagination">
                {{ $legalisasis->links() }}
            </div>
        @endif

    </section>
</div>

<div class="modal fade"
    id="legalisasiModal"
    tabindex="-1"
    aria-labelledby="legalisasiModalTitle"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered legalisasi-dialog">
        <div class="modal-content legalisasi-modal">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="legalisasiModalTitle">
                        Tambah Legalisasi
                    </h5>
                    <p class="mb-0">Lengkapi informasi dokumen di bawah ini.</p>
                </div>

                <button type="button"
                    class="legalisasi-back"
                    data-bs-dismiss="modal">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali</span>
                </button>
            </div>

            <form id="legalisasiForm"
                method="POST"
                action="{{ route('karyawan.legalisasi.store') }}">

                @csrf

                <input type="hidden" id="legalisasiMethod" value="POST">

                <div class="modal-body" tabindex="0">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="no_urut" class="form-label">
                                Nomor Urut
                            </label>

                            <input type="text"
                                class="form-control"
                                id="no_urut"
                                name="no_urut"
                                maxlength="20"
                                placeholder="Masukkan nomor urut"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">
                                Tanggal
                            </label>

                            <input type="date"
                                class="form-control"
                                id="tanggal"
                                name="tanggal"
                                required>
                        </div>

                        <div class="col-12">
                            <label for="sifat_surat" class="form-label">
                                Sifat Surat
                            </label>

                            <input type="text"
                                class="form-control"
                                id="sifat_surat"
                                name="sifat_surat"
                                maxlength="100"
                                placeholder="Masukkan sifat surat"
                                required>
                        </div>

                        <div class="col-12">
                            <div class="legalisasi-name-heading">
                                <label class="form-label mb-0">
                                    Nama Penghadap
                                </label>

                                <button type="button"
                                    id="addNamaPenghadap"
                                    class="btn-add-penghadap">
                                    <i class="fa-solid fa-plus"></i>
                                    Tambah Nama
                                </button>
                            </div>

                            <div id="namaPenghadapContainer"></div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn-legalisasi-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn-legalisasi-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade"
    id="hapusLegalisasiModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content legalisasi-modal">
            <div class="modal-body text-center p-4">

                <div class="legalisasi-delete-icon">
                    <i class="fa-solid fa-trash"></i>
                </div>

                <h5>Hapus Data Legalisasi?</h5>

                <p class="text-muted">
                    Data dengan nomor
                    <strong id="hapusNomorLegalisasi"></strong>
                    akan dihapus secara permanen.
                </p>

                <form id="hapusLegalisasiForm" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button"
                            class="btn-legalisasi-secondary"
                            data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn-legalisasi-danger">
                            Ya, Hapus
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.legalisasiData = @json($legalisasiData);

    window.legalisasiRoutes = {
        store: @json(route('karyawan.legalisasi.store')),
        update: @json(route('karyawan.legalisasi.update', ['legalisasi' => '__ID__'])),
        destroy: @json(route('karyawan.legalisasi.destroy', ['legalisasi' => '__ID__']))
    };
</script>

<script src="{{ asset('js/kelola_legalisasi.js') }}"></script>
@endpush

@endsection