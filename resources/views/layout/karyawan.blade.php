<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Homepage Karyawan - SIANOTA')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">

    @stack('css')
</head>

<body>

<div class="app-shell">

    <aside class="sidebar" id="sidebar">

        <div class="brand">

            <div class="brand-icon">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>

            <div class="brand-text">
                <strong>SIANOTA</strong>
            </div>

        </div>

        <div class="sidebar-menu">

            <div class="nav-title">
                Menu Utama
            </div>

            <a href="{{ route('karyawan.homepage') }}" class="nav-link {{ request()->routeIs('karyawan.homepage') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Homepage</span>
            </a>

            <div class="nav-title second-title">
                Data Master
            </div>

            <a href="{{ route('karyawan.skmht.kelola_skmht') }}"
            class="nav-link {{ request()->routeIs('karyawan.skmht.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-signature"></i>
                <span>Kelola SKMHT</span>
            </a>

            <a href="{{ route('karyawan.legalisasi.kelola') }}"
                class="nav-link {{ request()->routeIs('karyawan.legalisasi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-stamp"></i>
                <span>Kelola Legalisasi</span>
            </a>

        </div>

    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main">

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="mobile-menu"
                    id="sidebarToggle"
                    aria-label="Buka menu"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>

            </div>

            <div class="topbar-right">

                <div class="notification-wrapper" data-notifications-url="{{ route('notifications.index') }}" data-read-url="{{ route('notifications.read', ['notification' => '__ID__']) }}" data-read-all-url="{{ route('notifications.read_all') }}">
                    <button type="button" class="notification-button" id="notificationToggle" aria-label="Notifikasi">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notification-dot hidden" id="notificationDot"></span>
                    </button>

                    <div class="notification-menu" id="notificationMenu">
                        <div class="notification-header">
                            <div>
                                <strong>Notifikasi</strong>
                                <span id="notificationCount">0 belum dibaca</span>
                            </div>

                            <button type="button" class="notification-read-all" id="notificationReadAll">
                                Tandai semua dibaca
                            </button>
                        </div>

                        <div class="notification-list" id="notificationList">
                            <div class="notification-loading">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>Memuat notifikasi...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="topbar-divider"></div>

                <div class="profile-wrapper">

                    <button
                        type="button"
                        class="profile"
                        id="profileToggle"
                        aria-label="Menu profil"
                    >

                        <div class="profile-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                        </div>

                        <div class="profile-info">

                            <strong>
                                {{ auth()->user()->name ?? 'Karyawan' }}
                            </strong>

                            <span>
                                Karyawan
                            </span>

                        </div>

                        <i class="fa-solid fa-chevron-down profile-arrow"></i>

                    </button>

                    <div
                        class="profile-menu"
                        id="profileMenu"
                    >

                        <div class="profile-menu-header">

                            <div class="profile-menu-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                            </div>

                            <div>

                                <strong>
                                    {{ auth()->user()->name ?? 'Karyawan' }}
                                </strong>

                                <span>
                                    Karyawan
                                </span>

                            </div>

                        </div>

                        <div class="profile-menu-divider"></div>

                        <form
                            action="{{ route('logout') }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="profile-logout"
                            >
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Keluar</span>
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </header>

        <section class="content">

            @yield('content')

        </section>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/homepage.js') }}"></script>

@stack('scripts')

</body>
</html>