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

    function buatPenghadapInput(nama = '', bisaHapus = true) {
        const wrapper = document.createElement('div');
        wrapper.className = 'penghadap-input';

        const nomor = document.createElement('span');
        nomor.className = 'penghadap-number';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'nama_penghadap[]';
        input.className = 'form-control';
        input.placeholder = 'Nama penghadap';
        input.maxLength = 150;
        input.value = nama;
        input.required = true;

        const tombolHapus = document.createElement('button');
        tombolHapus.type = 'button';
        tombolHapus.className = 'remove-penghadap';
        tombolHapus.title = 'Hapus nama penghadap';
        tombolHapus.innerHTML = '<i class="fa-solid fa-xmark"></i>';

        if (!bisaHapus) {
            tombolHapus.style.display = 'none';
        }

        tombolHapus.addEventListener('click', function () {
            if (!bisaHapus) {
                return;
            }

            wrapper.remove();

            const container = document.getElementById('penghadapContainer');

            if (container) {
                perbaruiNomorPenghadap(container);
            }
        });

        wrapper.appendChild(nomor);
        wrapper.appendChild(input);
        wrapper.appendChild(tombolHapus);

        return wrapper;
    }

    function perbaruiNomorPenghadap(container) {
        const rows = container.querySelectorAll('.penghadap-input');

        rows.forEach(function (row, index) {
            const nomor = row.querySelector('.penghadap-number');
            const tombolHapus = row.querySelector('.remove-penghadap');

            if (nomor) {
                nomor.textContent = index + 1;
            }

            if (tombolHapus) {
                tombolHapus.disabled = rows.length <= 1;
            }
        });
    }

    function tambahBarisPenghadap(container, nama = '', bisaHapus = true) {
        if (!container) {
            return;
        }

        container.appendChild(
            buatPenghadapInput(nama, bisaHapus)
        );

        perbaruiNomorPenghadap(container);
    }

    function aturPenghadapInput(
        container,
        daftarNama = [],
        bisaHapus = true
    ) {
        if (!container) {
            return;
        }

        container.innerHTML = '';

        if (!Array.isArray(daftarNama) || daftarNama.length === 0) {
            daftarNama = [''];
        }

        daftarNama.forEach(function (nama) {
            tambahBarisPenghadap(
                container,
                nama,
                bisaHapus
            );
        });

        perbaruiNomorPenghadap(container);
    }

    const penghadapContainer = document.getElementById('penghadapContainer');
    const tambahPenghadap = document.getElementById('tambahPenghadap');

    if (penghadapContainer) {
        aturPenghadapInput(penghadapContainer, [''], true);
    }

    if (tambahPenghadap && penghadapContainer) {
        tambahPenghadap.addEventListener('click', function () {
            tambahBarisPenghadap(penghadapContainer, '', true);
        });
    }

    const detailModalElement = document.getElementById('detailSkmhtModal');

    if (detailModalElement) {
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

                const daftarNama = item.penghadap || [];

                detailPenghadap.innerHTML = '';

                if (daftarNama.length > 0) {
                    daftarNama.forEach(function (nama, index) {
                        const namaElement = document.createElement('div');

                        namaElement.className = 'detail-name';
                        namaElement.textContent = (index + 1) + '. ' + nama;

                        detailPenghadap.appendChild(namaElement);
                    });
                } else {
                    const namaElement = document.createElement('div');

                    namaElement.className = 'detail-name';
                    namaElement.textContent = 'Belum ada nama penghadap';

                    detailPenghadap.appendChild(namaElement);
                }

                detailJumlahPenghadap.textContent =
                    daftarNama.length + ' Penghadap';

                bootstrap.Modal.getOrCreateInstance(
                    detailModalElement
                ).show();
            });
        });
    }

    const editModalElement = document.getElementById('editSkmhtModal');
    const editForm = document.getElementById('formEditSkmht');
    const editContainer = document.getElementById('penghadapEditContainer');

    if (editModalElement && editForm && editContainer) {
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

                const daftarNama = item.penghadap || [];

                aturPenghadapInput(
                    editContainer,
                    daftarNama,
                    false
                );

                editForm.action = editForm.dataset.url.replace(':id', id);

                bootstrap.Modal.getOrCreateInstance(
                    editModalElement
                ).show();
            });
        });
    }

    const deleteForm = document.getElementById('formHapusSkmht');

    if (deleteForm) {
        document.querySelectorAll('.action-btn.delete').forEach(function (button) {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                deleteForm.action = deleteForm.dataset.url.replace(':id', id);
            });
        });
    }
});
