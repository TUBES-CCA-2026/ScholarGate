<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mengelola pendaftaran akun mahasiswa baru.
 */
class RegisterController extends Controller
{
    /**
     * Menampilkan halaman pendaftaran mahasiswa.
     */
    public function show(): View
    {
        return view('auth.register');
    }

    /**
     * Menyimpan akun mahasiswa dengan role student.
     *
     * Password tidak di-hash manual di controller karena model User sudah
     * menggunakan cast hashed pada atribut password.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50', 'unique:users,nim'],
            'program_studi' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:100'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $validated['role'] = User::ROLE_STUDENT;

        User::create($validated);

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil, silakan login.');
    }
}
