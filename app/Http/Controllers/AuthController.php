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
        $login = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($login)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('show_login_loading', true);
            }

            return redirect()->route('karyawan.homepage')->with('show_login_loading', true);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput();
    }

    public function register()
    {
        return view('register');
    }

    public function registerProcess(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'password' => 'required|min:6',
        ], [
            'name.required' => 'Username wajib diisi.',
            'name.max' => 'Username maksimal 100 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
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