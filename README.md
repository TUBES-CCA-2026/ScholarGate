# ScholarGate

ScholarGate adalah aplikasi Laravel untuk pengajuan berkas mahasiswa dan pengelolaan beasiswa atau program akademik oleh admin prodi.

Versi ini sudah diperbarui dengan:

- Landing page publik.
- Refactor clean code.
- Dokumentasi teknis.
- Middleware terpisah untuk `student` dan `admin`.
- Perbaikan form upload dokumen pengajuan.
- Fitur bookmark informasi beasiswa.
- Pembersihan CSS yang tidak dipakai oleh Blade aplikasi.
- Normalisasi migration database dan penghapusan tabel bawaan Laravel yang tidak dipakai fitur aplikasi.

## Dokumentasi

Baca dokumentasi lengkap di:

```text
docs/DOKUMENTASI_SCHOLARGATE.md
```

Baca daftar perubahan clean code di:

```text
docs/CLEAN_CODE_CHANGELOG.md
```

Baca catatan normalisasi database di:

```text
docs/DATABASE_NORMALIZATION.md
```

## Instalasi Singkat

Paket final ini sudah menyertakan `vendor` dan `node_modules` sesuai permintaan. Jika dependency ingin dibuat ulang, jalankan kembali perintah Composer dan NPM.

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

Pastikan database MySQL `scholargate` sudah dibuat atau sesuaikan konfigurasi database di `.env`.

## Akun dan Role

Role yang digunakan:

- `student` untuk mahasiswa.
- `admin` untuk admin prodi.

Seeder menyediakan akun contoh jika dijalankan melalui `php artisan db:seed`.

## Struktur Utama

```text
routes/web.php
app/Http/Controllers
app/Http/Middleware
app/Models
database/migrations
database/seeders
resources/views
public/css/scholargate.css
docs
```

## Catatan Database

Tabel domain yang dipertahankan adalah `users`, `document_types`, `requirements`, `student_applications`, `application_documents`, `announcements`, dan `bookmarks`.

Tabel bawaan Laravel seperti `sessions`, `cache`, `jobs`, `job_batches`, `failed_jobs`, dan `password_reset_tokens` tidak lagi dibuat karena fitur aplikasi saat ini tidak menggunakannya. Konfigurasi sudah diarahkan ke `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, dan `QUEUE_FAILED_DRIVER=null`.

## Catatan Distribusi

Paket ini tetap menyertakan `.env`, `.git`, `vendor`, dan `node_modules` karena diminta agar semua folder dari ZIP awal tetap lengkap. Untuk distribusi production atau repository normal, file dan folder tersebut biasanya tidak dikirim langsung. File `.env` berisi konfigurasi lokal dan sebaiknya tidak dibagikan ke pihak yang tidak berkepentingan.
