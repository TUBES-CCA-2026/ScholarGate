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
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
            'nim' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/', 'unique:users,nim'],
            'program_studi' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:100'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:10', 'max:15'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, dan tanda baca standar.',
            'nim.regex' => 'NIM hanya boleh berisi angka.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'phone.min' => 'Nomor HP minimal 10 digit.',
            'phone.max' => 'Nomor HP maksimal 15 digit.',
        ]);

        $validated['role'] = User::ROLE_STUDENT;

        User::create($validated);

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil, silakan login.');
    }
}
