# Normalisasi Database ScholarGate

## Tujuan

Normalisasi dilakukan untuk memastikan tabel yang tersisa benar-benar dipakai oleh fitur aplikasi, mengurangi migration patch yang berulang, dan menjaga relasi data agar tidak menghasilkan data yatim atau duplikat.

## Tabel Domain yang Dipertahankan

| Tabel | Fungsi |
|---|---|
| `users` | Akun mahasiswa dan admin, termasuk profil mahasiswa. |
| `document_types` | Master beasiswa atau jenis pengajuan. |
| `requirements` | Syarat dokumen per master beasiswa. |
| `student_applications` | Header pengajuan mahasiswa. |
| `application_documents` | Detail dokumen per requirement pada satu pengajuan. |
| `announcements` | Pengumuman untuk mahasiswa. |
| `bookmarks` | Relasi simpan informasi antara mahasiswa dan master beasiswa. |

## Tabel yang Tidak Lagi Dibuat

| Tabel | Alasan Penghapusan |
|---|---|
| `password_reset_tokens` | Tidak ada fitur reset password yang memakai tabel ini pada kode aplikasi. |
| `sessions` | Session diarahkan ke driver `file`. |
| `cache` | Cache diarahkan ke driver `file`. |
| `cache_locks` | Tidak diperlukan setelah cache database tidak digunakan. |
| `jobs` | Queue diarahkan ke `sync`. |
| `job_batches` | Tidak ada batch queue yang digunakan. |
| `failed_jobs` | Tidak diperlukan setelah queue database tidak digunakan. |

## Perubahan Migration

1. Kolom profil mahasiswa dimasukkan langsung ke migration `users`.
2. Kolom gambar master beasiswa `image_path` dimasukkan langsung ke migration `document_types`.
3. Migration patch yang sebelumnya menambah kolom yang sama dihapus agar `migrate:fresh` tidak rawan gagal.
4. Foreign key menggunakan cascade delete pada relasi yang memang mengikuti data induk.
5. Constraint unik diterapkan pada `bookmarks` untuk mencegah penyimpanan ganda oleh user yang sama.
6. Constraint unik diterapkan pada pasangan `document_type_id` dan `name` di tabel `requirements` untuk menghindari nama syarat ganda dalam satu master beasiswa.

## Konfigurasi Pendukung

Agar skema database tidak lagi membutuhkan tabel bawaan Laravel yang tidak dipakai, konfigurasi berikut diterapkan pada `.env`, `.env.example`, dan file config terkait.

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
QUEUE_FAILED_DRIVER=null
```

## Catatan Migrasi Data Lama

Perubahan ini cocok untuk basis data baru melalui `php artisan migrate:fresh` atau `php artisan migrate` pada instalasi kosong. Jika sudah ada database production lama, lakukan backup terlebih dahulu dan buat migration transisi khusus agar data lama tidak hilang.

## Catatan Kompatibilitas MySQL
Pada tabel `application_documents`, unique constraint gabungan antara `student_application_id` dan `requirement_id` memakai nama eksplisit `app_docs_app_req_unique`. Nama ini sengaja dibuat pendek karena MySQL membatasi panjang identifier constraint. Tanpa nama eksplisit, Laravel akan membuat nama otomatis yang terlalu panjang dan migration dapat gagal pada MySQL.
