<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Provider aplikasi utama ScholarGate.
 *
 * Saat ini aplikasi belum membutuhkan binding service khusus. Class ini tetap
 * dipertahankan sebagai titik resmi untuk mendaftarkan service container,
 * macro, atau konfigurasi bootstrapping Laravel pada pengembangan berikutnya.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Mendaftarkan dependency atau binding service container aplikasi.
     */
    public function register(): void
    {
        // Belum ada binding khusus yang perlu didaftarkan.
    }

    /**
     * Menjalankan konfigurasi setelah seluruh service provider terdaftar.
     */
    public function boot(): void
    {
        // Belum ada proses bootstrapping khusus untuk aplikasi ini.
    }
}
