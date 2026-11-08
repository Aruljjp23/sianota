document.addEventListener('DOMContentLoaded', function () {
    const data = window.skmhtData || [];

    const filterForm = document.getElementById('filterSkmht');
    const searchInput = document.getElementById('searchSkmht');
    const filterTahun = document.getElementById('filterTahun');

    if (filterForm) {
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(function () {
                    filterForm.submit();
                }, 500);
            });
        }

        if (filterTahun) {
            filterTahun.addEventListener('change', function () {
                filterForm.submit();
            });
        }
    }

    function buatPenghadapInput(nama = '') {
        const wrapper = document.createElement('div');
        wrapper.className = 'penghadap-input';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'nama_penghadap';
        input.className = 'form-control';
        input.placeholder = 'Nama penghadap';
        input.maxLength = 150;
        input.value = nama;
        input.required = true;

        wrapper.appendChild(input);

        return wrapper;
    }

    function aturPenghadapInput(container, nama = '') {
        if (!container) {
            return;
        }

        container.innerHTML = '';
        container.appendChild(buatPenghadapInput(nama));
    }

    const penghadapContainer = document.getElementById('penghadapContainer');
    const tambahPenghadap = document.getElementById('tambahPenghadap');

    if (penghadapContainer) {
        aturPenghadapInput(penghadapContainer);

        if (tambahPenghadap) {
            tambahPenghadap.style.display = 'none';
        }
    }

    const detailModalElement = document.getElementById('detailSkmhtModal');

    if (detailModalElement) {
        const detailModal = bootstrap.Modal.getOrCreateInstance(
            detailModalElement
        );

        const detailNomorBulanan = document.getElementById('detailNomorBulanan');
        const detailTanggalAkta = document.getElementById('detailTanggalAkta');
        const detailSifatJenis = document.getElementById('detailSifatJenis');
        const detailJam = document.getElementById('detailJam');
        const detailAlamat = document.getElementById('detailAlamat');
        const detailPenghadap = document.getElementById('detailPenghadap');
        const detailJumlahPenghadap = document.getElementById('detailJumlahPenghadap');

        document.querySelectorAll('.action-btn.detail').forEach(function (button) {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                const item = data.find(function (dataItem) {
                    return String(dataItem.id_skmht) === String(id);
                });

                if (!item) {
                    return;
                }

                detailNomorBulanan.textContent = item.nomor_bulanan || '-';
                detailTanggalAkta.textContent = item.tanggal_tampil || '-';
                detailSifatJenis.textContent = item.sifat_jenis_akta || '-';
                detailJam.textContent = item.jam || '-';
                detailAlamat.textContent = item.alamat || '-';

                const namaPenghadap = item.nama_penghadap || '';

                if (detailPenghadap) {
                    detailPenghadap.innerHTML = '';

                    const namaElement = document.createElement('div');
                    namaElement.className = 'detail-name';
                    namaElement.textContent = namaPenghadap || 'Belum ada nama penghadap';

                    detailPenghadap.appendChild(namaElement);
                }

                if (detailJumlahPenghadap) {
                    detailJumlahPenghadap.textContent =
                        namaPenghadap ? '1 Penghadap' : '0 Penghadap';
                }

                detailModal.show();
            });
        });
    }

    const editModalElement = document.getElementById('editSkmhtModal');
    const editForm = document.getElementById('formEditSkmht');
    const editContainer = document.getElementById('penghadapEditContainer');

    if (editModalElement && editForm && editContainer) {
        const editModal = bootstrap.Modal.getOrCreateInstance(
            editModalElement
        );

        const editNomorBulanan = document.getElementById('editNomorBulanan');
        const editTanggalAkta = document.getElementById('editTanggalAkta');
        const editSifatJenis = document.getElementById('editSifatJenis');
        const editJam = document.getElementById('editJam');
        const editAlamat = document.getElementById('editAlamat');

        document.querySelectorAll('.action-btn.edit').forEach(function (button) {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                const item = data.find(function (dataItem) {
                    return String(dataItem.id_skmht) === String(id);
                });

                if (!item) {
                    return;
                }

                editNomorBulanan.value = item.nomor_bulanan || '';
                editTanggalAkta.value = item.tanggal_akta || '';
                editSifatJenis.value = item.sifat_jenis_akta || '';
                editJam.value = item.jam || '';
                editAlamat.value = item.alamat || '';

                aturPenghadapInput(
                    editContainer,
                    item.nama_penghadap || ''
                );

                editForm.action = editForm.dataset.url.replace(
                    ':id',
                    id
                );

                editModal.show();
            });
        });
    }

    const deleteForm = document.getElementById('formHapusSkmht');

    if (deleteForm) {
        document.querySelectorAll('.action-btn.delete').forEach(function (button) {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                deleteForm.action = deleteForm.dataset.url.replace(
                    ':id',
                    id
                );
            });
        });
    }
});