<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| File ini menyimpan closure command sederhana yang dapat dijalankan melalui
| Artisan. ScholarGate belum memiliki command domain khusus, sehingga command
| bawaan Laravel dipertahankan sebagai contoh struktur console route.
|
*/

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
