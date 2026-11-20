<?php

namespace Tests\Feature;

use App\Models\Legalisasi;
use App\Models\Penghadap;
use App\Models\Skmht;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class LaporanControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        # Mendukung fungsi YEAR() pada SQLite.
        DB::connection()->getPdo()->sqliteCreateFunction(
            'YEAR',
            fn ($tanggal) => substr($tanggal, 0, 4),
            1
        );
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    private function karyawan(): User
    {
        return User::create([
            'name' => 'Karyawan Test',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);
    }

    private function buatSkmht(array $data = []): Skmht
    {
        return Skmht::create(array_merge([
            'nomor_bulanan' => '01/IX/2026',
            'tanggal_akta' => '2026-09-10',
            'sifat_jenis_akta' => 'Pemberian Hak Tanggungan',
            'alamat' => 'Jl. Merdeka No. 10',
            'jam' => '10:00:00',
        ], $data));
    }

    private function buatLegalisasi(array $data = []): Legalisasi
    {
        return Legalisasi::create(array_merge([
            'no_urut' => '01/2026',
            'tanggal' => '2026-09-12',
            'sifat_surat' => 'Legalisasi Dokumen',
        ], $data));
    }

    private function tambahPenghadapSkmht(
        Skmht $skmht,
        string $nama
    ): Penghadap {
        return Penghadap::create([
            'id_skmht' => $skmht->id_skmht,
            'nama_penghadap' => $nama,
        ]);
    }

    private function tambahPenghadapLegalisasi(
        Legalisasi $legalisasi,
        string $nama
    ): Penghadap {
        return Penghadap::create([
            'id_legalisasi' => $legalisasi->id_legalisasi,
            'nama_penghadap' => $nama,
        ]);
    }

    # WB-LAPORAN-01
    public function test_admin_dapat_membuka_halaman_laporan(): void
    {
        $this->actingAs($this->admin());

        $response = $this->get(route('laporan.data_laporan'));

        $response->assertOk();
        $response->assertViewIs('admin.laporan.data_laporan');
    }

    # WB-LAPORAN-02
    public function test_karyawan_tidak_dapat_membuka_halaman_laporan(): void
    {
        $this->actingAs($this->karyawan());

        $response = $this->get(route('laporan.data_laporan'));

        $response->assertForbidden();
    }

    # WB-LAPORAN-03
    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $response = $this->get(route('laporan.data_laporan'));

        $response->assertRedirect(route('login'));
    }

    # WB-LAPORAN-04
    public function test_jenis_laporan_default_adalah_skmht(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht();

        $response = $this->get(route('laporan.data_laporan'));

        $response->assertOk();
        $response->assertViewHas('jenis', 'skmht');
        $response->assertViewHas('laporan');
    }

    # WB-LAPORAN-05
    public function test_admin_dapat_menampilkan_laporan_legalisasi(): void
    {
        $this->actingAs($this->admin());

        $this->buatLegalisasi();

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'legalisasi',
        ]));

        $response->assertOk();
        $response->assertViewHas('jenis', 'legalisasi');
        $response->assertViewHas('laporan');
    }

    # WB-LAPORAN-06
    public function test_pencarian_laporan_skmht_berdasarkan_nomor(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht([
            'nomor_bulanan' => '01/IX/2026',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
        ]);

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'skmht',
            'search' => '01/IX/2026',
        ]));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertCount(1, $laporan->getCollection());
        $this->assertEquals(
            '01/IX/2026',
            $laporan->getCollection()->first()['nomor']
        );
    }

    # WB-LAPORAN-07
    public function test_pencarian_laporan_berdasarkan_nama_penghadap(): void
    {
        $this->actingAs($this->admin());

        $skmht = $this->buatSkmht();

        $this->tambahPenghadapSkmht($skmht, 'Andi Saputra');

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
        ]);

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'skmht',
            'search' => 'Andi Saputra',
        ]));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertCount(1, $laporan->getCollection());
        $this->assertStringContainsString(
            'Andi Saputra',
            $laporan->getCollection()->first()['penghadap']
        );
    }

    # WB-LAPORAN-08
    public function test_pencarian_laporan_skmht_berdasarkan_alamat(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht([
            'alamat' => 'Jl. Merdeka Madiun',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
            'alamat' => 'Jl. Diponegoro Madiun',
        ]);

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'skmht',
            'search' => 'Merdeka',
        ]));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertCount(1, $laporan->getCollection());
    }

    # WB-LAPORAN-09
    public function test_filter_sifat_laporan_berjalan(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht([
            'sifat_jenis_akta' => 'Pemberian Hak Tanggungan',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
            'sifat_jenis_akta' => 'Surat Kuasa',
        ]);

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'skmht',
            'sifat' => 'Pemberian Hak Tanggungan',
        ]));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertCount(1, $laporan->getCollection());
    }

    # WB-LAPORAN-10
    public function test_filter_tahun_laporan_berjalan(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht([
            'tanggal_akta' => '2025-09-10',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
            'tanggal_akta' => '2026-09-10',
        ]);

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'skmht',
            'tahun' => 2026,
        ]));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertCount(1, $laporan->getCollection());
    }

    # WB-LAPORAN-11
    public function test_filter_tanggal_mulai_dan_tanggal_selesai_berjalan(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht([
            'tanggal_akta' => '2026-09-01',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
            'tanggal_akta' => '2026-09-15',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '03/IX/2026',
            'tanggal_akta' => '2026-09-30',
        ]);

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'skmht',
            'tanggal_mulai' => '2026-09-10',
            'tanggal_selesai' => '2026-09-20',
        ]));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertCount(1, $laporan->getCollection());
    }

    # WB-LAPORAN-12
    public function test_daftar_sifat_dan_tahun_laporan_ditampilkan(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht([
            'sifat_jenis_akta' => 'Pemberian Hak Tanggungan',
            'tanggal_akta' => '2025-09-10',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
            'sifat_jenis_akta' => 'Surat Kuasa',
            'tanggal_akta' => '2026-09-10',
        ]);

        $response = $this->get(route('laporan.data_laporan'));

        $response->assertOk();
        $response->assertViewHas('daftarSifat');
        $response->assertViewHas('daftarTahun');
    }

    # WB-LAPORAN-13
    public function test_jenis_laporan_tidak_valid_ditolak(): void
    {
        $this->actingAs($this->admin());

        $response = $this->get(route('laporan.data_laporan', [
            'jenis' => 'tidak-valid',
        ]));

        $response->assertSessionHasErrors('jenis');
    }

    # WB-LAPORAN-14
    public function test_tahun_di_luar_batas_validasi_ditolak(): void
    {
        $this->actingAs($this->admin());

        $response = $this->get(route('laporan.data_laporan', [
            'tahun' => 1800,
        ]));

        $response->assertSessionHasErrors('tahun');
    }

    # WB-LAPORAN-15
    public function test_pencarian_melebihi_batas_karakter_ditolak(): void
    {
        $this->actingAs($this->admin());

        $response = $this->get(route('laporan.data_laporan', [
            'search' => str_repeat('A', 151),
        ]));

        $response->assertSessionHasErrors('search');
    }

    # WB-LAPORAN-16
    public function test_format_tanggal_tidak_valid_ditolak(): void
    {
        $this->actingAs($this->admin());

        $response = $this->get(route('laporan.data_laporan', [
            'tanggal_mulai' => '10-09-2026',
        ]));

        $response->assertSessionHasErrors('tanggal_mulai');
    }

    # WB-LAPORAN-17
    public function test_tanggal_selesai_sebelum_tanggal_mulai_ditolak(): void
    {
        $this->actingAs($this->admin());

        $response = $this->get(route('laporan.data_laporan', [
            'tanggal_mulai' => '2026-09-20',
            'tanggal_selesai' => '2026-09-10',
        ]));

        $response->assertSessionHasErrors('tanggal_selesai');
    }

    # WB-LAPORAN-18
    public function test_laporan_diurutkan_dari_tanggal_terbaru(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht([
            'nomor_bulanan' => '01/IX/2026',
            'tanggal_akta' => '2026-09-01',
        ]);

        $this->buatSkmht([
            'nomor_bulanan' => '02/IX/2026',
            'tanggal_akta' => '2026-09-20',
        ]);

        $response = $this->get(route('laporan.data_laporan'));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertEquals(
            '02/IX/2026',
            $laporan->getCollection()->first()['nomor']
        );
    }

    # WB-LAPORAN-19
    public function test_laporan_menggunakan_pagination_sepuluh_data(): void
    {
        $this->actingAs($this->admin());

        for ($i = 1; $i <= 12; $i++) {
            $this->buatSkmht([
                'nomor_bulanan' => sprintf('%02d/IX/2026', $i),
                'tanggal_akta' => '2026-09-10',
            ]);
        }

        $response = $this->get(route('laporan.data_laporan'));

        $response->assertOk();

        $laporan = $response->viewData('laporan');

        $this->assertCount(10, $laporan->getCollection());
        $this->assertEquals(12, $laporan->total());
    }

    # WB-LAPORAN-20
    public function test_admin_dapat_mencetak_pdf_laporan_skmht(): void
    {
        $this->actingAs($this->admin());

        $this->buatSkmht();

        $pdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);

        $pdf->shouldReceive('setPaper')
            ->once()
            ->with('a4', 'landscape')
            ->andReturnSelf();

        $pdf->shouldReceive('download')
            ->once()
            ->with('laporan-skmht.pdf')
            ->andReturn(response('', 200, [
                'Content-Disposition' => 'attachment; filename=laporan-skmht.pdf',
            ]));

        Pdf::shouldReceive('loadView')
            ->once()
            ->with(
                'admin.laporan.cetak_pdf',
                Mockery::on(function ($data) {
                    return $data['jenis'] === 'skmht'
                        && $data['judul'] === 'LAPORAN DATA SKMHT';
                })
            )
            ->andReturn($pdf);

        $response = $this->get(route('laporan.pdf', [
            'jenis' => 'skmht',
        ]));

        $response->assertOk();

        $response->assertHeader(
            'Content-Disposition',
            'attachment; filename=laporan-skmht.pdf'
        );
    }
}
