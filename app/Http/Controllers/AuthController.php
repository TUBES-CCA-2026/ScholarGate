<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Mengelola proses login dan logout pengguna ScholarGate.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Memvalidasi kredensial, membuat session baru, lalu mengarahkan pengguna sesuai role.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak sesuai.'])
                ->onlyInput('email');
        }

        if ($remember) {
            cookie()->queue('remember_email', $request->email, 43200); // 30 hari
        } else {
            cookie()->queue(cookie()->forget('remember_email'));
        }

        $request->session()->regenerate();

        return $this->redirectToDashboard($request);
    }

    /**
     * Mengakhiri session pengguna dengan aman.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /**
     * Menentukan dashboard tujuan berdasarkan role pengguna aktif.
     */
    private function redirectToDashboard(Request $request): RedirectResponse
    {
        return $request->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.home');
    }
}
