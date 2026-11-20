<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    # WB-AUTH-01
    public function test_login_username_dan_password_kosong(): void
    {
        $response = $this->post(route('login.process'), [
            'name' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'password',
        ]);
    }

    # WB-AUTH-02
    public function test_login_username_tidak_terdaftar(): void
    {
        $response = $this->post(route('login.process'), [
            'name' => 'username_salah',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    # WB-AUTH-03
    public function test_login_password_salah(): void
    {
        User::create([
            'name' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.process'), [
            'name' => 'admin',
            'password' => 'password_salah',
        ]);

        $response->assertSessionHasErrors('password');
    }

    # WB-AUTH-04
    public function test_login_admin_berhasil(): void
    {
        User::create([
            'name' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.process'), [
            'name' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    # WB-AUTH-05
    public function test_login_karyawan_berhasil(): void
    {
        User::create([
            'name' => 'karyawan',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
        ]);

        $response = $this->post(route('login.process'), [
            'name' => 'karyawan',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('karyawan.homepage'));

        $this->assertAuthenticated();
    }

    # WB-AUTH-06
    public function test_register_username_kosong(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    # WB-AUTH-07
    public function test_register_password_kurang_dari_6_karakter(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'userbaru',
            'password' => '12345',
        ]);

        $response->assertSessionHasErrors('password');
    }

    # WB-AUTH-08
    public function test_register_user_pertama_mendapat_role_admin(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'adminpertama',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'adminpertama',
            'role' => 'admin',
        ]);
    }

    # WB-AUTH-09
    public function test_register_user_baru_mendapat_role_karyawan(): void
    {
        User::create([
            'name' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('register.process'), [
            'name' => 'karyawanbaru',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'karyawanbaru',
            'role' => 'karyawan',
        ]);
    }

    # WB-AUTH-10
    public function test_register_data_valid_berhasil_disimpan(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'uservalid',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'uservalid',
        ]);
    }

    # WB-AUTH-11
    public function test_logout_berhasil(): void
    {
        $user = User::create([
            'name' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
