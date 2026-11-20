<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function adminOnly(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'admin',
            403
        );
    }

    public function dataUser(Request $request)
    {
        $this->adminOnly();

        $search = trim($request->input('search', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalUser = User::count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalKaryawan = User::where('role', 'karyawan')->count();

        return view('admin.users.data_user', compact(
            'users',
            'totalUser',
            'totalAdmin',
            'totalKaryawan'
        ));
    }

    public function store(Request $request)
    {
        $this->adminOnly();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/\A[a-zA-Z0-9]+\z/',
                'unique:users,name',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^[A-Za-z0-9!@#$%&*_-]+$/',
            ],

            'role' => [
                'required',
                Rule::in(['admin', 'karyawan']),
            ],
        ], [
            'name.required' => 'Username wajib diisi.',
            'name.max' => 'Username maksimal 100 karakter.',
            'name.regex' => 'Username hanya boleh menggunakan huruf dan angka tanpa spasi atau simbol.',
            'name.unique' => 'Username sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'password.regex' => 'Password mengandung karakter yang tidak diperbolehkan atau spasi.',

            'role.required' => 'Role user wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        User::create([
            'name' => $validated['name'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('users.data_user')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $this->adminOnly();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/\A[a-zA-Z0-9]+\z/',
                Rule::unique('users', 'name')->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'regex:/^[A-Za-z0-9!@#$%&*_-]+$/',
            ],

            'role' => [
                'required',
                Rule::in(['admin', 'karyawan']),
            ],
        ], [
            'name.required' => 'Username wajib diisi.',
            'name.max' => 'Username maksimal 100 karakter.',
            'name.regex' => 'Username hanya boleh menggunakan huruf dan angka tanpa spasi atau simbol.',
            'name.unique' => 'Username sudah digunakan.',

            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
            'password.regex' => 'Password tidak boleh mengandung spasi atau karakter yang tidak diperbolehkan.',

            'role.required' => 'Role user wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        $user->name = $validated['name'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('users.data_user')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->adminOnly();

        if ((int) $user->id === (int) Auth::id()) {
            return redirect()
                ->route('users.data_user')
                ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()
            ->route('users.data_user')
            ->with('success', 'User berhasil dihapus.');
    }
}