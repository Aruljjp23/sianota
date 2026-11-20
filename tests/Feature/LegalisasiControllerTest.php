<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Legalisasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegalisasiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            DB::connection()->getPdo()->sqliteCreateFunction(
                'YEAR',
                fn ($tanggal) => substr($tanggal, 0, 4),
                1
            );
        }
    }

    # Membuat akun admin untuk pengujian
    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    # Membuat akun karyawan untuk pengujian
    private function karyawan(): User
    {
        return User::create([
            'name' => 'Karyawan Test',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);
    }

    # Membuat data legalisasi untuk pengujian
    private function legalisasi()
    {
        $legalisasi = Legalisasi::create([
            'no_urut' => '01/2026',
            'tanggal' => '2026-01-10',
            'sifat_surat' => 'Surat Kuasa',
        ]);

        $legalisasi->penghadap()->create([
            'nama_penghadap' => 'Nama Penghadap',
        ]);

        return $legalisasi;
    }

    # WB-LEGAL-01
    public function test_admin_dapat_mengakses_data_legaliasi()
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->get(route('legalisasi.data_legalisasi'));

        $response->assertStatus(200);
    }

    # WB-LEGAL-02
    public function test_admin_dapat_mengakses_kelola_legaliasi()
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->get(route('legalisasi.kelola'));

        $response->assertStatus(200);
    }

    # WB-LEGAL-03
    public function test_karyawan_tidak_dapat_mengakses_data_legalisasi_admin()
    {
        $karyawan = $this->karyawan();

        $response = $this->actingAs($karyawan)
            ->get(route('legalisasi.data_legalisasi'));

        $response->assertStatus(403);
    }

    # WB-LEGAL-04
    public function test_pengguna_belum_login_diarahkan_ke_login()
    {
        $response = $this->get(route('legalisasi.data_legalisasi'));

        $response->assertRedirect(route('login'));
    }

    # WB-LEGAL-05
    public function test_karyawan_dapat_mengakses_kelola_legalisasi()
    {
        $karyawan = $this->karyawan();

        $response = $this->actingAs($karyawan)
            ->get(route('karyawan.legalisasi.kelola'));

        $response->assertStatus(200);
    }

    # WB-LEGAL-06
    public function test_admin_tidak_dapat_mengakses_kelola_legalisasi_karyawan()
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->get(route('karyawan.legalisasi.kelola'));

        $response->assertStatus(403);
    }

    # WB-LEGAL-07
    public function test_pencarian_legaliasi_berdasarkan_nomor_urut()
    {
        $admin = $this->admin();

        $this->legalisasi();

        $response = $this->actingAs($admin)
            ->get(route('legalisasi.data_legalisasi', [
                'search' => '01/2026',
            ]));

        $response->assertStatus(200);

        $response->assertViewHas('legalisasis', function ($data) {
            return $data->count() === 1;
        });
    }

    # WB-LEGAL-08
    public function test_pencarian_legaliasi_berdasarkan_sifat_surat()
    {
        $admin = $this->admin();

        $this->legalisasi();

        $response = $this->actingAs($admin)
            ->get(route('legalisasi.data_legalisasi', [
                'search' => 'Surat Kuasa',
            ]));

        $response->assertStatus(200);

        $response->assertViewHas('legalisasis', function ($data) {
            return $data->count() === 1;
        });
    }

    # WB-LEGAL-09
    public function test_pencarian_legaliasi_berdasarkan_nama_penghadap()
    {
        $admin = $this->admin();

        $this->legalisasi();

        $response = $this->actingAs($admin)
            ->get(route('legalisasi.data_legalisasi', [
                'search' => 'Nama Penghadap',
            ]));

        $response->assertStatus(200);

        $response->assertViewHas('legalisasis', function ($data) {
            return $data->count() === 1;
        });
    }

    # WB-LEGAL-10
    public function test_filter_tahun_legaliasi()
    {
        $admin = $this->admin();

        Legalisasi::create([
            'no_urut' => '01/2025',
            'tanggal' => '2025-01-10',
            'sifat_surat' => 'Surat Tahun 2025',
        ]);

        Legalisasi::create([
            'no_urut' => '01/2026',
            'tanggal' => '2026-01-10',
            'sifat_surat' => 'Surat Tahun 2026',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('legalisasi.data_legalisasi', [
                'tahun' => 2026,
            ]));

        $response->assertStatus(200);

        $response->assertViewHas('legalisasis', function ($data) {
            return $data->count() === 1
                && $data->first()->no_urut === '01/2026';
        });
    }

    # WB-LEGAL-11
    public function test_daftar_tahun_legaliasi_ditampilkan()
    {
        $admin = $this->admin();

        Legalisasi::create([
            'no_urut' => '01/2025',
            'tanggal' => '2025-01-10',
            'sifat_surat' => 'Surat Tahun 2025',
        ]);

        Legalisasi::create([
            'no_urut' => '01/2026',
            'tanggal' => '2026-01-10',
            'sifat_surat' => 'Surat Tahun 2026',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('legalisasi.data_legalisasi'));

        $response->assertStatus(200);

        $response->assertViewHas('tahunList', function ($data) {
            return $data->contains(2025)
                && $data->contains(2026);
        });
    }

    # WB-LEGAL-12
    public function test_nomor_urut_kosong_ditolak()
    {
        $karyawan = $this->karyawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.legalisasi.store'), [
                'no_urut' => '',
                'tanggal' => '2026-03-10',
                'sifat_surat' => 'Surat Perjanjian',
                'nama_penghadap' => ['Penghadap'],
            ]);

        $response->assertSessionHasErrors('no_urut');
    }

    # WB-LEGAL-13
    public function test_tanggal_kosong_ditolak()
    {
        $karyawan = $this->karyawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.legalisasi.store'), [
                'no_urut' => '02/2026',
                'tanggal' => '',
                'sifat_surat' => 'Surat Perjanjian',
                'nama_penghadap' => ['Penghadap'],
            ]);

        $response->assertSessionHasErrors('tanggal');
    }

    # WB-LEGAL-14
    public function test_sifat_surat_kosong_ditolak()
    {
        $karyawan = $this->karyawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.legalisasi.store'), [
                'no_urut' => '03/2026',
                'tanggal' => '2026-03-10',
                'sifat_surat' => '',
                'nama_penghadap' => ['Penghadap'],
            ]);

        $response->assertSessionHasErrors('sifat_surat');
    }

    # WB-LEGAL-15
    public function test_nama_penghadap_kosong_ditolak()
    {
        $karyawan = $this->karyawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.legalisasi.store'), [
                'no_urut' => '04/2026',
                'tanggal' => '2026-03-10',
                'sifat_surat' => 'Surat Perjanjian',
                'nama_penghadap' => [],
            ]);

        $response->assertSessionHasErrors('nama_penghadap');
    }

    # WB-LEGAL-16
    public function test_nama_penghadap_spasi_dibersihkan_dan_ditolak()
    {
        $user = $this->karyawan();

        $response = $this->actingAs($user)->post(
            route('karyawan.legalisasi.store'),
            [
                'no_urut' => '06/2026',
                'tanggal' => '2026-06-10',
                'sifat_surat' => 'Surat Perjanjian',
                'nama_penghadap' => ['', '   '],
            ]
        );

        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('legalisasi', [
            'no_urut' => '06/2026',
        ]);
    }

    # WB-LEGAL-17
    public function test_karyawan_dapat_menyimpan_data_legaliasi()
    {
        $karyawan = $this->karyawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.legalisasi.store'), [
                'no_urut' => '06/2026',
                'tanggal' => '2026-03-10',
                'sifat_surat' => 'Surat Perjanjian',
                'nama_penghadap' => [
                    'Penghadap Pertama',
                    'Penghadap Kedua',
                ],
            ]);

        $response->assertRedirect(route('karyawan.legalisasi.kelola'));

        $this->assertDatabaseHas('legalisasi', [
            'no_urut' => '06/2026',
        ]);

        $legalisasi = Legalisasi::where('no_urut', '06/2026')->first();

        $this->assertEquals(2, $legalisasi->penghadap()->count());
    }

    # WB-LEGAL-18
    public function test_karyawan_dapat_mengubah_data_legaliasi()
    {
        $karyawan = $this->karyawan();
        $legalisasi = $this->legalisasi();

        $response = $this->actingAs($karyawan)
            ->put(route('karyawan.legalisasi.update', $legalisasi), [
                'no_urut' => '07/2026',
                'tanggal' => '2026-04-10',
                'sifat_surat' => 'Surat Kuasa Baru',
                'nama_penghadap' => ['Penghadap Update'],
            ]);

        $response->assertRedirect(route('karyawan.legalisasi.kelola'));

        $this->assertDatabaseHas('legalisasi', [
            'id_legalisasi' => $legalisasi->id_legalisasi,
            'no_urut' => '07/2026',
            'sifat_surat' => 'Surat Kuasa Baru',
        ]);
    }

    # WB-LEGAL-19
    public function test_karyawan_dapat_mengubah_data_penghadap()
    {
        $karyawan = $this->karyawan();
        $legalisasi = $this->legalisasi();

        $response = $this->actingAs($karyawan)
            ->put(route('karyawan.legalisasi.update', $legalisasi), [
                'no_urut' => '08/2026',
                'tanggal' => '2026-04-10',
                'sifat_surat' => 'Surat Kuasa',
                'nama_penghadap' => [
                    'Penghadap Baru Satu',
                    'Penghadap Baru Dua',
                ],
            ]);

        $response->assertRedirect(route('karyawan.legalisasi.kelola'));

        $this->assertDatabaseHas('penghadap', [
            'id_legalisasi' => $legalisasi->id_legalisasi,
            'nama_penghadap' => 'Penghadap Baru Satu',
        ]);

        $this->assertDatabaseHas('penghadap', [
            'id_legalisasi' => $legalisasi->id_legalisasi,
            'nama_penghadap' => 'Penghadap Baru Dua',
        ]);

        $this->assertDatabaseMissing('penghadap', [
            'id_legalisasi' => $legalisasi->id_legalisasi,
            'nama_penghadap' => 'Nama Penghadap',
        ]);
    }

    # WB-LEGAL-20
    public function test_karyawan_dapat_menghapus_data_legaliasi()
    {
        $karyawan = $this->karyawan();
        $legalisasi = $this->legalisasi();

        $response = $this->actingAs($karyawan)
            ->delete(route('karyawan.legalisasi.destroy', $legalisasi));

        $response->assertRedirect(route('karyawan.legalisasi.kelola'));

        $this->assertDatabaseMissing('legalisasi', [
            'id_legalisasi' => $legalisasi->id_legalisasi,
        ]);
    }
}