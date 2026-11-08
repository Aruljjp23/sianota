document.addEventListener('DOMContentLoaded', function () {
    const dataSkmht = window.skmhtData || [];

    function getSkmht(id) {
        return dataSkmht.find(function (item) {
            return String(item.id_skmht) === String(id);
        });
    }

    function setMetode(method) {
        const methodContainer = document.getElementById('skmhtMethod');

        if (!methodContainer) return;

        methodContainer.replaceChildren();

        if (method) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_method';
            input.value = method;

            methodContainer.appendChild(input);
        }
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.detail-skmht');

        if (!button) return;

        const item = getSkmht(button.dataset.id);

        if (!item) {
            console.error('Data SKMHT tidak ditemukan:', button.dataset.id);
            return;
        }

        const modalElement = document.getElementById('detailSkmhtModal');

        if (!modalElement || !window.bootstrap) {
            console.error('Modal detail atau Bootstrap belum dimuat.');
            return;
        }

        const isi = {
            detailNomor: item.nomor_bulanan || '-',
            detailTanggal: item.tanggal_format || '-',
            detailSifat: item.sifat_jenis_akta || '-',
            detailAlamat: item.alamat || '-',
            detailNama: item.nama_penghadap || '-'
        };

        Object.keys(isi).forEach(function (id) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = isi[id];
            }
        });

        bootstrap.Modal.getOrCreateInstance(modalElement).show();
    });

    const form = document.getElementById('skmhtForm');
    const modalElement = document.getElementById('skmhtModal');
    const modalTitle = document.getElementById('skmhtModalTitle');
    const btnTambah = document.getElementById('btnTambahSkmht');

    if (form && modalElement && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const storeUrl = form.action;

        const inputNama = form.elements.namedItem('nama_penghadap');

        if (btnTambah) {
            btnTambah.addEventListener('click', function () {
                form.reset();

                form.action = storeUrl;
                setMetode(null);

                if (modalTitle) {
                    modalTitle.textContent = 'Tambah SKMHT';
                }

                modal.show();
            });
        }

        document.querySelectorAll('.edit-skmht').forEach(function (button) {
            button.addEventListener('click', function () {
                const item = getSkmht(button.dataset.id);

                if (!item) return;

                form.reset();

                form.elements.namedItem('nomor_bulanan').value =
                    item.nomor_bulanan || '';

                form.elements.namedItem('tanggal_akta').value =
                    item.tanggal_akta || '';

                form.elements.namedItem('sifat_jenis_akta').value =
                    item.sifat_jenis_akta || '';

                form.elements.namedItem('alamat').value =
                    item.alamat || '';

                if (inputNama) {
                    inputNama.value = item.nama_penghadap || '';
                }

                form.action = storeUrl + '/' + item.id_skmht;

                setMetode('PUT');

                if (modalTitle) {
                    modalTitle.textContent = 'Edit Data SKMHT';
                }

                modal.show();
            });
        });
    }

    const deleteForm = document.getElementById('hapusSkmhtForm');
    const deleteElement = document.getElementById('hapusSkmhtModal');

    if (deleteForm && deleteElement && form && window.bootstrap) {
        const deleteModal = bootstrap.Modal.getOrCreateInstance(deleteElement);
        const storeUrl = form.action;

        document.querySelectorAll('.delete-skmht').forEach(function (button) {
            button.addEventListener('click', function () {
                const item = getSkmht(button.dataset.id);

                if (!item) return;

                deleteForm.action = storeUrl + '/' + item.id_skmht;

                const hapusNomor = document.getElementById('hapusNomor');

                if (hapusNomor) {
                    hapusNomor.textContent = item.nomor_bulanan || '-';
                }

                deleteModal.show();
            });
        });
    }

    const alertSuccess = document.querySelector('.skmht-alert-success');

    if (alertSuccess) {
        window.setTimeout(function () {
            alertSuccess.remove();
        }, 5000);
    }

    const filterForm = document.querySelector('.skmht-filter-form');

    if (filterForm) {
        const searchInput = filterForm.querySelector('[name="search"]');

        const filterInputs = filterForm.querySelectorAll(
            '[name="tahun"], [name="tanggal_dari"], [name="tanggal_sampai"]'
        );

        let filterTimer;

        function jalankanFilter() {
            clearTimeout(filterTimer);

            const tanggalDari = filterForm.querySelector('[name="tanggal_dari"]')?.value;
            const tanggalSampai = filterForm.querySelector('[name="tanggal_sampai"]')?.value;

            if (tanggalDari && tanggalSampai && tanggalDari > tanggalSampai) {
                return;
            }

            const params = new URLSearchParams(new FormData(filterForm));

            params.delete('page');

            const query = params.toString();
            const url = filterForm.action;

            window.location.href = query ? url + '?' + query : url;
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(filterTimer);
                filterTimer = setTimeout(jalankanFilter, 400);
            });
        }

        filterInputs.forEach(function (input) {
            input.addEventListener('change', jalankanFilter);
        });

        filterForm.addEventListener('submit', function (event) {
            event.preventDefault();
            jalankanFilter();
        });
    }
});