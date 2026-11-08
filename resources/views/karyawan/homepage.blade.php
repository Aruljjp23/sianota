
@extends('layout.karyawan')

@section('title', 'Homepage Karyawan - SIANOTA')

@section('content')

@if (session('show_login_loading'))
    <div class="login-success-message" id="loginSuccessMessage">
        <div class="login-success-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <div class="login-success-text">
            <strong>Login berhasil!</strong>
            <span>Selamat datang di halaman karyawan SIANOTA.</span>
        </div>

        <button type="button"
            class="login-success-close"
            onclick="this.closest('.login-success-message').remove()"
            aria-label="Tutup pesan">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

<div class="homepage-content">

    <div class="welcome">
        <div>
            <span class="welcome-label">Selamat datang</span>
            <h2>Halo, {{ auth()->user()->name ?? 'Karyawan' }}</h2>
            {{-- <p>Klik dokumen di bawah untuk melihat dan mengelola data.</p> --}}
        </div>
    </div>

    <div class="document-heading">
        <div>
            <h3>Dokumen</h3>
            <p>Sistem Informasi Pengelolaan Data Notaris</p>
        </div>
    </div>

    <div class="document-cards">

        <a href="{{ route('karyawan.skmht.kelola_skmht') }}"
            class="document-card skmht-card">

            <div class="document-card-top">
                <div class="big-document-icon">
                    <i class="fa-solid fa-file-signature"></i>
                </div>

                <span class="card-arrow">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </span>
            </div>

            <div class="document-card-content">
                <span class="document-number">Dokumen 01</span>
                <h3>SKMHT</h3>
                <p>Surat Kuasa Membebankan Hak Tanggungan.</p>
            </div>

            <div class="document-card-footer">
                <span>
                    {{ number_format($totalSkmht, 0, ',', '.') }} dokumen
                </span>

                <span class="open-document">
                    Kelola data
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
        </a>

        <a href="{{ route('karyawan.legalisasi.kelola') }}" class="document-card legalisasi-card">

            <div class="document-card-top">
                <div class="big-document-icon">
                    <i class="fa-solid fa-stamp"></i>
                </div>

                <span class="card-arrow">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </span>
            </div>

            <div class="document-card-content">
                <span class="document-number">Dokumen 02</span>
                <h3>Legalisasi</h3>
                <p>Data surat yang telah melalui proses legalisasi.</p>
            </div>

            <div class="document-card-footer">
                <span>
                    {{ number_format($totalLegalisasi, 0, ',', '.') }} dokumen
                </span>

                <span class="open-document">
                    Kelola data
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
        </a>

    </div>

</div>
@endsection