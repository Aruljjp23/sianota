<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Skmht;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkmhtControllerTest extends TestCase
{
    use RefreshDatabase;

    private function buatAdmin()
    {
        return User::create([
            'name' => 'Admin Test',
            'password' => 'password123',
            'role' => 'admin',
        ]);
    }

    private function buatKaryawan()
    {
        return User::create([
            'name' => 'Karyawan Test',
            'password' => 'password123',
            'role' => 'karyawan',
        ]);
    }

    private function buatSkmht()
    {
        $skmht = Skmht::create([
            'nomor_bulanan' => '01/IX/2026',
            'tanggal_akta' => '2026-09-10',
            'sifat_jenis_akta' => 'SKMHT',
            'alamat' => 'Jl. Test Madiun',
            'jam' => '09:00',
        ]);

        $skmht->penghadap()->createMany([
            ['nama_penghadap' => 'Nama Penghadap Test'],
            ['nama_penghadap' => 'Nama Penghadap Kedua'],
        ]);

        return $skmht;
    }

    # WB-SKMHT-01
    public function test_admin_dapat_mengakses_data_skmht()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->get(route('skmht.data_skmht'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.skmht.data_skmht');
    }

    # WB-SKMHT-02
    public function test_karyawan_tidak_dapat_mengakses_data_skmht_admin()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->get(route('skmht.data_skmht'));

        $response->assertStatus(403);
    }

    # WB-SKMHT-03
    public function test_pengguna_belum_login_diarahkan_ke_login()
    {
        $response = $this->get(route('skmht.data_skmht'));

        $response->assertRedirect(route('login'));
    }

    # WB-SKMHT-04
    public function test_karyawan_dapat_mengakses_kelola_skmht()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->get(route('karyawan.skmht.kelola_skmht'));

        $response->assertStatus(200);
        $response->assertViewIs('karyawan.skmht.kelola_skmht');
    }

    # WB-SKMHT-05
    public function test_admin_tidak_dapat_mengakses_kelola_skmht_karyawan()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->get(route('karyawan.skmht.kelola_skmht'));

        $response->assertStatus(403);
    }

    # WB-SKMHT-06
    public function test_karyawan_dapat_melihat_detail_skmht()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->getJson(route('karyawan.skmht.show', $skmht->id_skmht));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'nomor_bulanan' => '01/IX/2026',
        ]);
        $response->assertJsonFragment([
            'nama_penghadap' => 'Nama Penghadap Test',
        ]);
    }

    # WB-SKMHT-07
    public function test_pencarian_skmht_dapat_menampilkan_data_yang_sesuai()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->get(route('karyawan.skmht.kelola_skmht', [
                'search' => 'Nama Penghadap Test',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('skmhts', function ($data) use ($skmht) {
            return $data->contains('id_skmht', $skmht->id_skmht);
        });
    }

    # WB-SKMHT-08
    public function test_filter_sifat_jenis_akta()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->get(route('karyawan.skmht.kelola_skmht', [
                'sifat' => 'SKMHT',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('skmhts', function ($data) use ($skmht) {
            return $data->contains('id_skmht', $skmht->id_skmht);
        });
    }

    # WB-SKMHT-09
    public function test_filter_tahun_akta()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->get(route('karyawan.skmht.kelola_skmht', [
                'tahun' => '2026',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('skmhts', function ($data) use ($skmht) {
            return $data->contains('id_skmht', $skmht->id_skmht);
        });
    }

    # WB-SKMHT-10
    public function test_filter_tanggal_dari()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->get(route('karyawan.skmht.kelola_skmht', [
                'tanggal_dari' => '2026-09-01',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('skmhts', function ($data) use ($skmht) {
            return $data->contains('id_skmht', $skmht->id_skmht);
        });
    }

    # WB-SKMHT-11
    public function test_filter_tanggal_sampai()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->get(route('karyawan.skmht.kelola_skmht', [
                'tanggal_sampai' => '2026-09-30',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('skmhts', function ($data) use ($skmht) {
            return $data->contains('id_skmht', $skmht->id_skmht);
        });
    }

    # WB-SKMHT-12
    public function test_karyawan_dapat_menyimpan_data_skmht()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.skmht.store'), [
                'nomor_bulanan' => '02/IX/2026',
                'tanggal_akta' => '2026-09-15',
                'sifat_jenis_akta' => 'SKMHT',
                'alamat' => 'Jl. Madiun',
                'jam' => '10:00',
                'nama_penghadap' => [
                    'Penghadap Baru',
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('skmht', [
            'nomor_bulanan' => '02/IX/2026',
        ]);
    }

    # WB-SKMHT-13
    public function test_karyawan_dapat_menyimpan_beberapa_penghadap()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.skmht.store'), [
                'nomor_bulanan' => '03/IX/2026',
                'tanggal_akta' => '2026-09-20',
                'sifat_jenis_akta' => 'SKMHT',
                'alamat' => 'Jl. Test',
                'jam' => '11:00',
                'nama_penghadap' => [
                    'Penghadap Pertama',
                    'Penghadap Kedua',
                    'Penghadap Ketiga',
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $skmht = Skmht::where('nomor_bulanan', '03/IX/2026')->first();

        $this->assertNotNull($skmht);

        $this->assertDatabaseHas('penghadap', [
            'id_skmht' => $skmht->id_skmht,
            'nama_penghadap' => 'Penghadap Pertama',
        ]);

        $this->assertDatabaseHas('penghadap', [
            'id_skmht' => $skmht->id_skmht,
            'nama_penghadap' => 'Penghadap Kedua',
        ]);

        $this->assertDatabaseHas('penghadap', [
            'id_skmht' => $skmht->id_skmht,
            'nama_penghadap' => 'Penghadap Ketiga',
        ]);
    }

    # WB-SKMHT-14
    public function test_nomor_bulanan_kosong_ditolak()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.skmht.store'), [
                'nomor_bulanan' => '',
                'tanggal_akta' => '2026-09-15',
                'sifat_jenis_akta' => 'SKMHT',
                'alamat' => 'Jl. Madiun',
                'jam' => '10:00',
                'nama_penghadap' => [
                    'Penghadap Baru',
                ],
            ]);

        $response->assertSessionHasErrors('nomor_bulanan');
    }

    # WB-SKMHT-15
    public function test_tanggal_akta_kosong_ditolak()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.skmht.store'), [
                'nomor_bulanan' => '04/IX/2026',
                'tanggal_akta' => '',
                'sifat_jenis_akta' => 'SKMHT',
                'alamat' => 'Jl. Madiun',
                'jam' => '10:00',
                'nama_penghadap' => [
                    'Penghadap Baru',
                ],
            ]);

        $response->assertSessionHasErrors('tanggal_akta');
    }

    # WB-SKMHT-16
    public function test_sifat_jenis_akta_kosong_ditolak()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.skmht.store'), [
                'nomor_bulanan' => '05/IX/2026',
                'tanggal_akta' => '2026-09-15',
                'sifat_jenis_akta' => '',
                'alamat' => 'Jl. Madiun',
                'jam' => '10:00',
                'nama_penghadap' => [
                    'Penghadap Baru',
                ],
            ]);

        $response->assertSessionHasErrors('sifat_jenis_akta');
    }

    # WB-SKMHT-17
    public function test_nama_penghadap_tidak_diisi_ditolak()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.skmht.store'), [
                'nomor_bulanan' => '06/IX/2026',
                'tanggal_akta' => '2026-09-15',
                'sifat_jenis_akta' => 'SKMHT',
                'alamat' => 'Jl. Madiun',
                'jam' => '10:00',
            ]);

        $response->assertSessionHasErrors('nama_penghadap');
    }

    # WB-SKMHT-18
    public function test_nama_penghadap_kosong_dibersihkan_dan_ditolak()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->post(route('karyawan.skmht.store'), [
                'nomor_bulanan' => '07/IX/2026',
                'tanggal_akta' => '2026-09-15',
                'sifat_jenis_akta' => 'SKMHT',
                'alamat' => 'Jl. Madiun',
                'jam' => '10:00',
                'nama_penghadap' => [
                    '',
                    '   ',
                ],
            ]);

        $response->assertSessionHasErrors('nama_penghadap');
    }

    # WB-SKMHT-19
    public function test_karyawan_dapat_mengubah_data_skmht_dan_penghadap()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->put(route('karyawan.skmht.update', $skmht->id_skmht), [
                'nomor_bulanan' => '08/IX/2026',
                'tanggal_akta' => '2026-09-25',
                'sifat_jenis_akta' => 'SKMHT Baru',
                'alamat' => 'Alamat Baru',
                'jam' => '13:00',
                'nama_penghadap' => [
                    'Penghadap Update',
                    'Penghadap Kedua Update',
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('skmht', [
            'id_skmht' => $skmht->id_skmht,
            'nomor_bulanan' => '08/IX/2026',
            'sifat_jenis_akta' => 'SKMHT Baru',
        ]);

        $this->assertDatabaseHas('penghadap', [
            'id_skmht' => $skmht->id_skmht,
            'nama_penghadap' => 'Penghadap Update',
        ]);

        $this->assertDatabaseHas('penghadap', [
            'id_skmht' => $skmht->id_skmht,
            'nama_penghadap' => 'Penghadap Kedua Update',
        ]);

        $this->assertDatabaseMissing('penghadap', [
            'id_skmht' => $skmht->id_skmht,
            'nama_penghadap' => 'Nama Penghadap Test',
        ]);
    }

    # WB-SKMHT-20
    public function test_karyawan_dapat_menghapus_data_skmht()
    {
        $karyawan = $this->buatKaryawan();
        $skmht = $this->buatSkmht();

        $response = $this->actingAs($karyawan)
            ->delete(route('karyawan.skmht.destroy', $skmht->id_skmht));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('skmht', [
            'id_skmht' => $skmht->id_skmht,
        ]);
    }
}