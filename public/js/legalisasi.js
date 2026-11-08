document.addEventListener('DOMContentLoaded', function () {
    const data = window.legalisasiData || [];

    const detailButtons = document.querySelectorAll('.btn-detail-legalisasi');
    const detailModalElement = document.getElementById('detailLegalisasiModal');

    if (!detailModalElement || !window.bootstrap) {
        return;
    }

    const detailModal = bootstrap.Modal.getOrCreateInstance(detailModalElement);

    const detailNoUrut = document.getElementById('detailNoUrutLegalisasi');
    const detailTanggal = document.getElementById('detailTanggalLegalisasi');
    const detailSifatSurat = document.getElementById('detailSifatSuratLegalisasi');
    const detailNamaPenghadap = document.getElementById('detailNamaPenghadapLegalisasi');

    detailButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            const item = data.find(function (dataItem) {
                return String(dataItem.id_legalisasi) === String(id);
            });

            if (!item) {
                console.error('Data Legalisasi tidak ditemukan:', id);
                return;
            }

            if (detailNoUrut) {
                detailNoUrut.textContent = item.no_urut || '-';
            }

            if (detailTanggal) {
                detailTanggal.textContent = item.tanggal_format || '-';
            }

            if (detailSifatSurat) {
                detailSifatSurat.textContent = item.sifat_surat || '-';
            }

            if (detailNamaPenghadap) {
                detailNamaPenghadap.textContent =
                    item.nama_penghadap || 'Belum ada nama';
            }

            detailModal.show();
        });
    });
});