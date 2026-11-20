<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private function buatAdmin()
    {
        return User::create([
            'name' => 'admin_testing',
            'password' => 'password123',
            'role' => 'admin',
        ]);
    }

    private function buatKaryawan()
    {
        return User::create([
            'name' => 'karyawan_testing',
            'password' => 'password123',
            'role' => 'karyawan',
        ]);
    }

    # WB-USER-01
    public function test_admin_dapat_mengakses_halaman_data_user()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->get(route('users.data_user'));

        $response->assertOk();
        $response->assertViewIs('admin.users.data_user');
    }

    # WB-USER-02
    public function test_karyawan_tidak_dapat_mengakses_halaman_data_user()
    {
        $karyawan = $this->buatKaryawan();

        $response = $this->actingAs($karyawan)
            ->get(route('users.data_user'));

        $response->assertForbidden();
    }

    # WB-USER-03
    public function test_pengguna_belum_login_diarahkan_ke_halaman_login()
    {
        $response = $this->get(route('users.data_user'));

        $response->assertRedirect(route('login'));
    }

    # WB-USER-04
    public function test_admin_dapat_mencari_data_user()
    {
        $admin = $this->buatAdmin();

        User::create([
            'name' => 'ArulTesting',
            'password' => 'password123',
            'role' => 'karyawan',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('users.data_user', [
                'search' => 'ArulTesting',
            ]));

        $response->assertOk();
        $response->assertViewIs('admin.users.data_user');

        $response->assertViewHas('users', function ($users) {
            return $users->contains('name', 'ArulTesting')
                && !$users->contains('name', 'admin_testing');
        });
    }

    # WB-USER-05
    public function test_admin_dapat_menambahkan_user_dengan_data_valid()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'UserBaru',
                'password' => 'password123',
                'role' => 'karyawan',
            ]);

        $response->assertRedirect(route('users.data_user'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'UserBaru',
            'role' => 'karyawan',
        ]);
    }

    # WB-USER-06
    public function test_admin_tidak_dapat_menambahkan_user_dengan_nama_kosong()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => '',
                'password' => 'password123',
                'role' => 'karyawan',
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('users', [
            'name' => '',
        ]);
    }

    # WB-USER-07
    public function test_admin_tidak_dapat_menambahkan_user_dengan_password_kurang_dari_6_karakter()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'UserBaru',
                'password' => '12345',
                'role' => 'karyawan',
            ]);

        $response->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', [
            'name' => 'UserBaru',
        ]);
    }

    # WB-USER-08
    public function test_admin_tidak_dapat_menambahkan_user_dengan_role_tidak_valid()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'UserBaru',
                'password' => 'password123',
                'role' => 'superadmin',
            ]);

        $response->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'name' => 'UserBaru',
        ]);
    }

    # WB-USER-09
    public function test_admin_dapat_memperbarui_data_user_tanpa_mengubah_password()
    {
        $admin = $this->buatAdmin();

        $user = User::create([
            'name' => 'UserLama',
            'password' => 'password123',
            'role' => 'karyawan',
        ]);

        $passwordLama = $user->password;

        $response = $this->actingAs($admin)
            ->put(route('users.update', $user->id), [
                'name' => 'UserDiperbarui',
                'password' => '',
                'role' => 'admin',
            ]);

        $response->assertRedirect(route('users.data_user'));
        $response->assertSessionHas('success');

        $user->refresh();

        $this->assertEquals('UserDiperbarui', $user->name);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals($passwordLama, $user->password);
    }

    # WB-USER-10
    public function test_admin_dapat_memperbarui_password_user()
    {
        $admin = $this->buatAdmin();

        $user = User::create([
            'name' => 'UserLama',
            'password' => 'password123',
            'role' => 'karyawan',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('users.update', $user->id), [
                'name' => 'UserLama',
                'password' => 'passwordbaru123',
                'role' => 'karyawan',
            ]);

        $response->assertRedirect(route('users.data_user'));
        $response->assertSessionHas('success');

        $user->refresh();

        $this->assertTrue(
            Hash::check('passwordbaru123', $user->password)
        );
    }

    # WB-USER-11
    public function test_admin_dapat_menghapus_user_lain()
    {
        $admin = $this->buatAdmin();

        $user = User::create([
            'name' => 'UserHapus',
            'password' => 'password123',
            'role' => 'karyawan',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $user->id));

        $response->assertRedirect(route('users.data_user'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    # WB-USER-12
    public function test_admin_tidak_dapat_menghapus_akun_sendiri()
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $admin->id));

        $response->assertRedirect(route('users.data_user'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'admin_testing',
        ]);
    }
}