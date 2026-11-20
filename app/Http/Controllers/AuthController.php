<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/\A[a-zA-Z0-9]+\z/',
            ],
            'password' => [
                'required',
                'string',
                'max:100',
                'regex:/\A[a-zA-Z0-9!#_.]+\z/',
            ],
        ], [
            'name.required' => 'Username wajib diisi.',
            'name.string' => 'Username harus berupa teks.',
            'name.max' => 'Username maksimal 100 karakter.',
            'name.regex' => 'Username hanya boleh menggunakan huruf dan angka tanpa spasi atau simbol.',

            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.max' => 'Password maksimal 100 karakter.',
            'password.regex' => 'Password tidak boleh mengandung spasi atau karakter yang tidak diizinkan.',
        ]);

        $user = User::where('name', $credentials['name'])->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'name' => 'Username tidak sesuai dengan data yang terdaftar.',
                ])
                ->withInput($request->only('name'));
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors([
                    'password' => 'Password yang kamu masukkan salah.',
                ])
                ->withInput($request->only('name'));
        }

        Auth::login($user);

        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.dashboard')
                ->with('show_login_loading', true);
        }

        return redirect()
            ->route('karyawan.homepage')
            ->with('show_login_loading', true);
    }

    public function register()
    {
        return view('register');
    }

    public function registerProcess(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/\A[a-zA-Z0-9]+\z/',
                'unique:users,name',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:100',
                'regex:/\A[a-zA-Z0-9!#_.]+\z/',
            ],
        ], [
            'name.required' => 'Username wajib diisi.',
            'name.string' => 'Username harus berupa teks.',
            'name.min' => 'Username minimal 3 karakter.',
            'name.max' => 'Username maksimal 100 karakter.',
            'name.regex' => 'Username hanya boleh menggunakan huruf dan angka tanpa spasi atau simbol.',
            'name.unique' => 'Username sudah digunakan. Silakan gunakan username lain.',

            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'password.regex' => 'Password hanya boleh menggunakan huruf, angka, dan simbol ! # _ . tanpa spasi.',
        ]);

        $role = User::exists() ? 'karyawan' : 'admin';

        User::create([
            'name' => $data['name'],
            'password' => $data['password'],
            'role' => $role,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil. Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}