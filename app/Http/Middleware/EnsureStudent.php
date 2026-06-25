<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi route mahasiswa agar tidak dapat dibuka oleh akun admin.
 */
class EnsureStudent
{
    /**
     * Menghentikan request non-student dengan respons 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== User::ROLE_STUDENT) {
            abort(403, 'Akses hanya untuk mahasiswa.');
        }

        return $next($request);
    }
}
