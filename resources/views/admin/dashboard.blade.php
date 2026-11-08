@extends('layout.admin')

@section('title', 'Dashboard Admin - SIANOTA')

@section('heading', 'Dashboard')

@section('subtitle', 'Ringkasan data dan aktivitas SIANOTA')

@section('content')

@if (session('show_login_loading'))
    <div class="login-success-message" id="loginSuccessMessage">
        <div class="login-success-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <div class="login-success-text">
            <strong>Login berhasil!</strong>
            <span>Selamat datang di dashboard SIANOTA.</span>
        </div>

        <button type="button"
            class="login-success-close"
            onclick="this.closest('.login-success-message').remove()"
            aria-label="Tutup pesan">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

<div class="welcome-card">
    <div class="welcome-content">
        <span class="welcome-label">Selamat datang kembali</span>

        <h2>Halo, {{ auth()->user()->name ?? 'Admin' }}</h2>

        {{-- <p>
            Pantau data pengguna dan dokumen notaris melalui SIANOTA.
        </p> --}}
    </div>

    <div class="date-card">
        <div class="date-icon">
            <i class="fa-regular fa-calendar"></i>
        </div>

        <div>
            <span>Hari ini</span>
            <strong id="currentDate">-</strong>
        </div>
    </div>

</div>

<div class="section-heading">
    <div>
        <h2>Ringkasan</h2>
        <p>Informasi data dalam sistem</p>
    </div>
</div>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <span class="stat-label">Total User</span>
                <h3>{{ $totalUser }}</h3>
            </div>

            <div class="stat-icon purple">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="stat-bottom">
            <span>User Terdaftar</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <span class="stat-label">Dokumen SKMHT</span>
                <h3>{{ $totalSkmht }}</h3>
            </div>

            <div class="stat-icon blue">
                <i class="fa-solid fa-file-signature"></i>
            </div>
        </div>

        <div class="stat-bottom">
            <span>Seluruh data SKMHT</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <span class="stat-label">Dokumen Legalisasi</span>
                <h3>{{ $totalLegalisasi }}</h3>
            </div>

            <div class="stat-icon orange">
                <i class="fa-solid fa-stamp"></i>
            </div>
        </div>

        <div class="stat-bottom">
            <span>Seluruh data legalisasi</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div>
                <span class="stat-label">Total Dokumen</span>
                <h3>{{ $totalSkmht + $totalLegalisasi }}</h3>
            </div>

            <div class="stat-icon green">
                <i class="fa-solid fa-folder-open"></i>
            </div>
        </div>

        <div class="stat-bottom">
            <span>SKMHT dan legalisasi</span>
        </div>
    </div>

</div>

<div class="content-grid">

    <section class="panel">
        <div class="panel-header">
            <div>
                <h3>Dokumen Terbaru</h3>
                <p>Data dokumen yang terakhir ditambahkan</p>
            </div>
        </div>

        <div class="document-list">

            @forelse ($dokumenTerbaru as $dokumen)
                <div class="document-item">

                    <div class="document-icon {{ $dokumen['jenis'] === 'SKMHT' ? 'blue' : 'orange' }}">
                        <i class="fa-solid {{ $dokumen['jenis'] === 'SKMHT' ? 'fa-file-signature' : 'fa-stamp' }}"></i>
                    </div>

                    <div class="document-detail">
                        <strong>{{ $dokumen['jenis'] }}</strong>
                        <span>{{ $dokumen['nama'] }}</span>
                    </div>

                    <div class="document-meta">
                        <strong>{{ $dokumen['tanggal'] }}</strong>
                        <span>Terbaru</span>
                    </div>

                </div>
            @empty
                <div class="empty-state">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>Belum ada dokumen yang ditambahkan.</p>
                </div>
            @endforelse

        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h3>Akses Cepat</h3>
                <p>Menu yang sering digunakan</p>
            </div>
        </div>

        <div class="quick-menu">

            <a href="{{ route('users.data_user') }}" class="quick-item">
                <div class="quick-icon purple">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div class="quick-content">
                    <strong>Data User</strong>
                    <span>Kelola akun pengguna</span>
                </div>

                <i class="fa-solid fa-chevron-right quick-arrow"></i>
            </a>

            <a href="{{ route('skmht.data_skmht') }}" class="quick-item">
                <div class="quick-icon blue">
                    <i class="fa-solid fa-file-signature"></i>
                </div>

                <div class="quick-content">
                    <strong>Data SKMHT</strong>
                    <span>Lihat data dokumen SKMHT</span>
                </div>

                <i class="fa-solid fa-chevron-right quick-arrow"></i>
            </a>

            <a href="{{ route('legalisasi.data_legalisasi') }}" class="quick-item">
                <div class="quick-icon orange">
                    <i class="fa-solid fa-stamp"></i>
                </div>

                <div class="quick-content">
                    <strong>Data Legalisasi</strong>
                    <span>Lihat data dokumen legalisasi</span>
                </div>

                <i class="fa-solid fa-chevron-right quick-arrow"></i>
            </a>

            <a href="{{ route('laporan.data_laporan') }}" class="quick-item">
                <div class="quick-icon green">
                    <i class="fa-solid fa-file-lines"></i>
                </div>

                <div class="quick-content">
                    <strong>Laporan</strong>
                    <span>Filter dan cetak laporan dokumen</span>
                </div>

                <i class="fa-solid fa-chevron-right quick-arrow"></i>
            </a>

        </div>
    </section>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateElement = document.getElementById('currentDate');

    if (dateElement) {
        dateElement.textContent = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).format(new Date());
    }
});
</script>

@endpush
