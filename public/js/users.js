document.addEventListener('DOMContentLoaded', function () {

    const userModal = document.getElementById('userModal');
    const deleteModal = document.getElementById('deleteModal');

    const userForm = document.getElementById('userForm');
    const userMethod = document.getElementById('userMethod');

    const userName = document.getElementById('userName');
    const userPassword = document.getElementById('userPassword');

    const roleAdmin = document.getElementById('roleAdmin');
    const roleKaryawan = document.getElementById('roleKaryawan');

    const modalLabel = document.getElementById('modalLabel');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');

    const passwordHelp = document.getElementById('passwordHelp');

    const deleteUserName = document.getElementById('deleteUserName');
    const deleteUserForm = document.getElementById('deleteUserForm');

    const btnDeleteConfirm = document.getElementById('btnDeleteConfirm');

    const btnTambahUser = document.getElementById('btnTambahUser');
    const btnTambahUserEmpty = document.getElementById('btnTambahUserEmpty');

    const btnCancelModal = document.getElementById('btnCancelModal');
    const modalClose = document.getElementById('modalClose');

    const btnDeleteCancel = document.getElementById('btnDeleteCancel');

    function openUserModal() {

        userModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        modalLabel.textContent = 'Tambah User';
        modalTitle.textContent = 'Tambah User';
        modalDescription.textContent = 'Tambahkan akun baru ke dalam sistem SIANOTA.';

        userForm.action = '/users';
        userMethod.value = 'POST';

        userName.value = '';
        userPassword.value = '';

        roleAdmin.checked = false;
        roleKaryawan.checked = false;

        userPassword.required = true;

        passwordHelp.textContent = 'Password wajib diisi untuk user baru.';
    }

    function closeUserModal() {

        userModal.classList.remove('show');
        document.body.style.overflow = '';

    }

    function openEditModal(button) {

        const id = button.dataset.id;
        const name = button.dataset.name;
        const role = button.dataset.role;

        userModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        modalLabel.textContent = 'Edit User';
        modalTitle.textContent = 'Edit User';
        modalDescription.textContent = 'Perbarui informasi akun pengguna SIANOTA.';

        userForm.action = `/users/${id}`;
        userMethod.value = 'PUT';

        userName.value = name;
        userPassword.value = '';

        roleAdmin.checked = role === 'admin';
        roleKaryawan.checked = role === 'karyawan';

        userPassword.required = false;

        passwordHelp.textContent = 'Kosongkan jika password tidak ingin diubah.';
    }

    function openDeleteModal(button) {

        const id = button.dataset.id;
        const name = button.dataset.name;

        deleteUserName.textContent = name;

        deleteUserForm.action = `/users/${id}`;

        deleteModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {

        deleteModal.classList.remove('show');
        document.body.style.overflow = '';

    }

    if (btnTambahUser) {
        btnTambahUser.addEventListener('click', openUserModal);
    }

    if (btnTambahUserEmpty) {
        btnTambahUserEmpty.addEventListener('click', openUserModal);
    }

    document.querySelectorAll('.btn-edit-user').forEach(function (button) {

        button.addEventListener('click', function () {
            openEditModal(button);
        });

    });

    document.querySelectorAll('.btn-delete-user').forEach(function (button) {

        button.addEventListener('click', function () {
            openDeleteModal(button);
        });

    });

    if (btnCancelModal) {
        btnCancelModal.addEventListener('click', closeUserModal);
    }

    if (modalClose) {
        modalClose.addEventListener('click', closeUserModal);
    }

    if (btnDeleteCancel) {
        btnDeleteCancel.addEventListener('click', closeDeleteModal);
    }

    if (btnDeleteConfirm) {

        btnDeleteConfirm.addEventListener('click', function () {
            deleteUserForm.submit();
        });

    }

    document.querySelectorAll('.user-modal-overlay').forEach(function (overlay) {

        overlay.addEventListener('click', closeUserModal);

    });

    document.querySelectorAll('.delete-modal-overlay').forEach(function (overlay) {

        overlay.addEventListener('click', closeDeleteModal);

    });

    document.querySelectorAll('.password-toggle').forEach(function (button) {

        button.addEventListener('click', function () {

            const targetId = button.dataset.target;
            const input = document.getElementById(targetId);
            const icon = button.querySelector('i');

            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';

            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);

        });

    });

    document.querySelectorAll('.alert-close').forEach(function (button) {

        button.addEventListener('click', function () {

            const alert = button.closest('.alert-box');

            if (alert) {
                alert.remove();
            }

        });

    });

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeUserModal();
            closeDeleteModal();
        }

    });

    const searchInput = document.querySelector('.user-search input');

    if (searchInput) {

        let searchTimer;

        searchInput.addEventListener('input', function () {

            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                searchInput.form.submit();
            }, 600);

        });

    }
});