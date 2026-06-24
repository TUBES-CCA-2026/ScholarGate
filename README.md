# ScholarGate

ScholarGate adalah aplikasi Laravel untuk pengajuan berkas mahasiswa dan pengelolaan beasiswa/program akademik oleh admin prodi.

Versi ini sudah digabungkan dengan:

- Landing page publik.
- Refactor clean code.
- Dokumentasi teknis.
- Perbaikan route dobel.
- Perbaikan form upload dokumen pengajuan.
- Perbaikan migration duplikat agar lebih aman untuk `migrate:fresh`.

## Dokumentasi

Baca dokumentasi lengkap di:

```text
docs/DOKUMENTASI_SCHOLARGATE.md
```

Baca daftar perubahan clean code di:

```text
docs/CLEAN_CODE_CHANGELOG.md
```

## Instalasi Singkat

```bash
composer install
npm install
cp .env.example .env
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

## Struktur Utama

```text
routes/web.php
app/Http/Controllers
app/Models
resources/views
public/css/scholargate.css
```

## Catatan Keamanan

File `.env`, `.git`, `vendor`, dan `node_modules` tidak disertakan dalam ZIP clean. Buat ulang dependency dengan `composer install` dan `npm install`.

## Catatan Distribusi Clean

Paket clean ini tidak menyertakan `.env`, `.git`, `vendor`, `node_modules`, `database.sqlite`, dan file dump SQL. Struktur database dijalankan melalui migration Laravel.
