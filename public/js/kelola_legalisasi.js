document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('legalisasiForm');
    const modalElement = document.getElementById('legalisasiModal');
    const deleteModalElement = document.getElementById('hapusLegalisasiModal');
    const container = document.getElementById('namaPenghadapContainer');
    const addNameButton = document.getElementById('addNamaPenghadap');
    const addButton = document.getElementById('btnTambahLegalisasi');
    const methodInput = document.getElementById('legalisasiMethod');
    const modalTitle = document.getElementById('legalisasiModalTitle');
    const deleteForm = document.getElementById('hapusLegalisasiForm');
    const deleteNumber = document.getElementById('hapusNomorLegalisasi');
    const searchInput = document.getElementById('legalisasiSearch');
    const yearSelect = document.getElementById('legalisasiTahun');
    const filterForm = document.getElementById('legalisasiFilter');

    if (!form || !modalElement || !container) {
        return;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    const deleteModal = deleteModalElement
        ? bootstrap.Modal.getOrCreateInstance(deleteModalElement)
        : null;

    const dataList = Array.isArray(window.legalisasiData)
        ? window.legalisasiData
        : [];

    const routes = window.legalisasiRoutes || {};

    function addNameInput(value = '') {
        const wrapper = document.createElement('div');
        wrapper.className = 'legalisasi-name-input';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'nama_penghadap[]';
        input.className = 'form-control';
        input.maxLength = 150;
        input.placeholder = 'Masukkan nama penghadap';
        input.value = value;
        input.required = true;

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn-remove-penghadap';
        removeButton.innerHTML = '<i class="fa-solid fa-trash"></i>';
        removeButton.setAttribute('aria-label', 'Hapus nama penghadap');

        removeButton.addEventListener('click', function () {
            const rows = container.querySelectorAll('.legalisasi-name-input');

            if (rows.length > 1) {
                wrapper.remove();
                updateRemoveButtons();
            }
        });

        wrapper.appendChild(input);
        wrapper.appendChild(removeButton);

        container.appendChild(wrapper);

        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.legalisasi-name-input');

        rows.forEach(function (row) {
            const button = row.querySelector('.btn-remove-penghadap');

            if (button) {
                button.hidden = rows.length <= 1;
            }
        });
    }

    function getNames(item) {
        let names = item.nama_penghadap;

        if (!Array.isArray(names)) {
            names = item.penghadap;
        }

        if (!Array.isArray(names)) {
            return [];
        }

        return names
            .map(function (name) {
                if (typeof name === 'object' && name !== null) {
                    return name.nama_penghadap || '';
                }

                return name || '';
            })
            .filter(function (name) {
                return String(name).trim() !== '';
            });
    }

    function resetForm() {
        form.reset();
        container.replaceChildren();

        const methodField = form.querySelector('input[name="_method"]');

        if (methodField) {
            methodField.remove();
        }

        if (methodInput) {
            methodInput.value = 'POST';
        }

        form.action = routes.store || form.action;

        if (addNameButton) {
            addNameButton.hidden = false;
            addNameButton.style.display = '';
        }

        addNameInput();

        updateRemoveButtons();
    }

    function findRecord(id) {
        return dataList.find(function (item) {
            return String(item.id_legalisasi) === String(id);
        });
    }

    if (addNameButton) {
        addNameButton.addEventListener('click', function () {
            addNameInput();

            const inputs = container.querySelectorAll('input');

            inputs[inputs.length - 1]?.focus();
        });
    }

    if (addButton) {
        addButton.addEventListener('click', function () {
            resetForm();

            modalTitle.textContent = 'Tambah Legalisasi';
        });
    }

    document.querySelectorAll('.btn-edit-legalisasi').forEach(function (button) {
        button.addEventListener('click', function () {
            const item = findRecord(button.dataset.id);

            if (!item) {
                console.error('Data legalisasi tidak ditemukan:', button.dataset.id);
                return;
            }

            resetForm();

            modalTitle.textContent = 'Edit Legalisasi';

            document.getElementById('no_urut').value = item.no_urut || '';
            document.getElementById('tanggal').value = item.tanggal || '';
            document.getElementById('sifat_surat').value = item.sifat_surat || '';

            container.replaceChildren();

            const names = getNames(item);

        
            if (names.length > 0) {
                names.forEach(function (name) {
                    addNameInput(name);
                });
            } else {
                addNameInput('');
            }

            if (addNameButton) {
                addNameButton.hidden = false;
                addNameButton.style.display = '';
            }

            if (methodInput) {
                methodInput.value = 'PUT';
            }

            let methodField = form.querySelector('input[name="_method"]');

            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                form.appendChild(methodField);
            }

            methodField.value = 'PUT';

            if (routes.update) {
                form.action = routes.update.replace(
                    '__ID__',
                    item.id_legalisasi
                );
            }

            modal.show();
        });
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        resetForm();

        modalTitle.textContent = 'Tambah Legalisasi';
    });

    document.querySelectorAll('.btn-hapus-legalisasi').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!deleteModal || !deleteForm) {
                return;
            }

            deleteNumber.textContent = button.dataset.nomor || '-';

            if (routes.destroy) {
                deleteForm.action = routes.destroy.replace(
                    '__ID__',
                    button.dataset.id
                );
            }

            deleteModal.show();
        });
    });

    let searchTimer;

    if (searchInput && filterForm) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                filterForm.requestSubmit();
            }, 450);
        });
    }

    if (yearSelect && filterForm) {
        yearSelect.addEventListener('change', function () {
            filterForm.requestSubmit();
        });
    }

    resetForm();
});