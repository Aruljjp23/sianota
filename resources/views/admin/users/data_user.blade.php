@extends('layout.admin')

@section('title', 'Data User')
{{-- @section('heading', 'Data User')
@section('subtitle', 'Ringkasan data dan aktivitas SIANOTA') --}}

@push('css')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')

<div class="users-page">

    <div class="users-header">

        <div class="users-title">

            <h2>Data User</h2>

        </div>

        <button
            type="button"
            class="btn-add-user"
            id="btnTambahUser"
        >
            <i class="fa-solid fa-plus"></i>
            <span>Tambah User</span>
        </button>

    </div>


    @if(session('success'))

        <div class="alert-box success">

            <div class="alert-icon">
                <i class="fa-solid fa-check"></i>
            </div>

            <span>
                {{ session('success') }}
            </span>

            <button
                type="button"
                class="alert-close"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert-box danger">

            <div class="alert-icon">
                <i class="fa-solid fa-exclamation"></i>
            </div>

            <span>
                {{ session('error') }}
            </span>

            <button
                type="button"
                class="alert-close"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

    @endif


    <div class="user-overview">

        <div class="overview-item">

            <div class="overview-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div class="overview-content">
                <span>Total User</span>
                <strong>{{ $totalUser }}</strong>
            </div>

        </div>


        <div class="overview-line"></div>


        <div class="overview-item">

            <div class="overview-icon admin-icon">
                <i class="fa-solid fa-user-shield"></i>
            </div>

            <div class="overview-content">
                <span>Admin</span>
                <strong>{{ $totalAdmin }}</strong>
            </div>

        </div>


        <div class="overview-line"></div>


        <div class="overview-item">

            <div class="overview-icon employee-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="overview-content">
                <span>Karyawan</span>
                <strong>{{ $totalKaryawan }}</strong>
            </div>

        </div>

    </div>


    <div class="users-card">

        <div class="users-card-header">

            <div class="users-card-title">

                <h3>
                    Daftar User
                </h3>

                {{-- <p>
                    Daftar akun yang terdaftar di SIANOTA
                </p> --}}

            </div>


            <form action="{{ route('users.data_user') }}" method="GET" class="user-search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau role..." autocomplete="off">

                @if(request('search'))

                    <a href="{{ route('users.data_user') }}" class="clear-search">
                        <i class="fa-solid fa-xmark"></i>
                    </a>

                @endif

                <button type="submit">
                    Cari
                </button>

            </form>

        </div>


        <div class="table-wrap">

            <table class="users-table">

                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($users as $index => $user)

                        <tr>

                            <td>
                                {{ $users->firstItem() + $index }}
                            </td>

                            <td>

                                <div class="user-name">

                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>

                                    <div class="user-name-info">

                                        <strong>
                                            {{ $user->name }}
                                        </strong>

                                        {{-- <span>
                                            Pengguna SIANOTA
                                        </span> --}}

                                    </div>

                                </div>

                            </td>

                            <td>

                                @if($user->role === 'admin')

                                    <span class="role-badge admin">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        Admin
                                    </span>

                                @else

                                    <span class="role-badge karyawan">
                                        <i class="fa-solid fa-user"></i>
                                        Karyawan
                                    </span>

                                @endif

                            </td>

                            <td>

                                <span class="date-text">
                                    {{ $user->created_at?->format('d M Y') ?? '-' }}
                                </span>

                            </td>

                            <td>

                                <div class="table-actions">

                                    <button
                                        type="button"
                                        class="action-edit btn-edit-user"
                                        title="Edit User"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-role="{{ $user->role }}"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="action-delete btn-delete-user"
                                        title="Hapus User"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-users">
                                    <div class="empty-icon">
                                        <i class="fa-solid fa-users"></i>
                                    </div>

                                    <h4>Belum ada data user</h4>

                                    <p>
                                        Belum ada pengguna yang terdaftar di sistem.
                                    </p>

                                    <button
                                        type="button"
                                        class="btn-add-user"
                                        id="btnTambahUserEmpty">
                                        <i class="fa-solid fa-plus"></i>
                                        <span>Tambah User</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse

            </table>

        </div>


        @if($users->hasPages())

            <div class="users-pagination">

                <div class="pagination-info">
                    Menampilkan
                    <strong>{{ $users->firstItem() }}</strong>
                    sampai
                    <strong>{{ $users->lastItem() }}</strong>
                    dari
                    <strong>{{ $users->total() }}</strong>
                    user
                </div>

                <div class="pagination-links">

                    @if($users->onFirstPage())
                        <span class="page-button disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="page-button">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif


                    @foreach($users->getUrlRange(max(1, $users->currentPage() - 1), min($users->lastPage(), $users->currentPage() + 1)) as $page => $url)

                        @if($page == $users->currentPage())
                            <span class="page-button active">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="page-button">
                                {{ $page }}
                            </a>
                        @endif

                    @endforeach


                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="page-button">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="page-button disabled">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif

                </div>

            </div>

        @endif

    </div>

</div>


<div class="user-modal" id="userModal">

    <div class="user-modal-overlay"></div>

    <div class="user-modal-box">

        <div class="user-modal-header">

            <div>

                <span
                    class="modal-label"
                    id="modalLabel"
                >
                    Tambah User
                </span>

                <h3 id="modalTitle">
                    Tambah User
                </h3>

                <p id="modalDescription">
                    Tambahkan akun baru ke dalam sistem SIANOTA.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                id="modalClose"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <form
            action="{{ route('users.store') }}"
            method="POST"
            id="userForm"
        >

            @csrf

            <input
                type="hidden"
                name="_method"
                id="userMethod"
                value="POST"
            >


            <div class="user-form-body">

                <div class="form-group">

                    <label for="userName">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="userName"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="off"
                        required
                    >

                </div>

                <div class="form-row">

                    <div class="form-group">

                        <label for="userPassword">
                            Password
                        </label>

                        <div class="password-input">

                            <input
                                type="password"
                                name="password"
                                id="userPassword"
                                placeholder="Masukkan password"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="userPassword"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>

                        </div>

                        <small id="passwordHelp">
                            Password wajib diisi untuk user baru.
                        </small>

                    </div>


                    <div class="form-group">

                        <label for="userPasswordConfirmation">
                            Konfirmasi Password
                        </label>

                        <div class="password-input">

                            <input
                                type="password"
                                name="password_confirmation"
                                id="userPasswordConfirmation"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="userPasswordConfirmation"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>

                        </div>

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Role User
                    </label>


                    <div class="role-options">

                        <label class="role-option">

                            <input
                                type="radio"
                                name="role"
                                value="admin"
                                id="roleAdmin"
                                required
                            >

                            <span class="role-option-content">

                                <span class="role-option-icon admin">
                                    <i class="fa-solid fa-user-shield"></i>
                                </span>

                                <span>
                                    <strong>Admin</strong>
                                    <small>Akses penuh ke sistem</small>
                                </span>

                            </span>

                        </label>


                        <label class="role-option">

                            <input
                                type="radio"
                                name="role"
                                value="karyawan"
                                id="roleKaryawan"
                            >

                            <span class="role-option-content">

                                <span class="role-option-icon karyawan">
                                    <i class="fa-solid fa-user"></i>
                                </span>

                                <span>
                                    <strong>Karyawan</strong>
                                    <small>Akses pengelolaan data</small>
                                </span>

                            </span>

                        </label>

                    </div>

                </div>

            </div>


            <div class="user-modal-footer">

                <button type="button" class="btn-modal-cancel" id="btnCancelModal">
                    Batal
                </button>


                <button type="submit" class="btn-modal-save" id="btnSaveUser">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan User</span>
                </button>

            </div>

        </form>

    </div>

</div>


<div class="delete-modal" id="deleteModal">

    <div class="delete-modal-overlay"></div>

    <div class="delete-modal-box">

        <div class="delete-icon">
            <i class="fa-solid fa-trash"></i>
        </div>

        <h3>
            Hapus User?
        </h3>

        <p>
            Apakah kamu yakin ingin menghapus user
            <strong id="deleteUserName"></strong>?
        </p>

        <span class="delete-warning">
            Data user yang dihapus tidak dapat dikembalikan.
        </span>


        <div class="delete-actions">

            <button type="button" class="btn-delete-cancel" id="btnDeleteCancel">
                Batal
            </button>


            <button type="button" class="btn-delete-confirm" id="btnDeleteConfirm">
                <i class="fa-solid fa-trash"></i>
                Hapus User
            </button>

        </div>

    </div>

</div>


<form method="POST" id="deleteUserForm" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="{{ asset('js/users.js') }}"></script>
@endpush