# Clean Code Changelog ScholarGate

Dokumen ini mencatat perubahan teknis yang dilakukan pada paket ScholarGate agar struktur kode lebih bersih, database lebih normal, dan file aplikasi lebih mudah dipelihara.

## 1. Route dan Middleware

File utama:

- `routes/web.php`
- `bootstrap/app.php`
- `app/Http/Middleware/EnsureAdmin.php`
- `app/Http/Middleware/EnsureStudent.php`

Perubahan:

1. Route publik, auth, mahasiswa, dan admin dipisahkan secara eksplisit.
2. Area mahasiswa kini memakai middleware `auth` dan `student`, sehingga akun admin tidak diarahkan ke fitur mahasiswa.
3. Area admin tetap memakai middleware `auth` dan `admin`.
4. Alias middleware didefinisikan langsung pada `bootstrap/app.php` agar Laravel 12 dapat memuatnya secara konsisten.
5. Komentar dokumentatif ditambahkan pada kelompok route agar fungsi setiap area mudah ditelusuri.

## 2. Controller Aplikasi

File utama:

- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/StudentDashboardController.php`
- `app/Http/Controllers/StudentApplicationController.php`
- `app/Http/Controllers/StudentBookmarkController.php`
- `app/Http/Controllers/Admin/AdminDashboardController.php`
- `app/Http/Controllers/Admin/AdminApplicationController.php`
- `app/Http/Controllers/Admin/AnnouncementController.php`
- `app/Http/Controllers/Admin/DocumentTypeController.php`

Perubahan:

1. Setiap controller diberi dokumentasi class dan method.
2. Validasi pada controller besar dipindahkan ke method khusus agar method utama lebih ringkas.
3. Logika kecil seperti pembuatan kode pengajuan, penghapusan file, sinkronisasi syarat, dan filter pencarian dipisahkan ke private method.
4. Status role, status pengajuan, dan status dokumen menggunakan constant model agar tidak bergantung pada string tersebar.
5. Operasi yang menyimpan lebih dari satu tabel, seperti pembuatan pengajuan dan pengelolaan master syarat, memakai database transaction.
6. Validasi syarat pada master beasiswa dibuat lebih aman dengan pencegahan nama syarat duplikat.

## 3. Model dan Relasi

File utama:

- `app/Models/User.php`
- `app/Models/DocumentType.php`
- `app/Models/Requirement.php`
- `app/Models/StudentApplication.php`
- `app/Models/ApplicationDocument.php`
- `app/Models/Announcement.php`
- `app/Models/Bookmark.php`

Perubahan:

1. Setiap model diberi dokumentasi fungsi tabel dan relasinya.
2. Constant role ditambahkan pada `User`.
3. Constant status ditambahkan pada `StudentApplication` dan `ApplicationDocument`.
4. Relasi Eloquent disusun sesuai struktur database normal.
5. Helper `isAdmin()` dan `isStudent()` dipertahankan untuk pemeriksaan role yang lebih jelas.
6. Model `Bookmark` menjadi penghubung normal antara `users` dan `document_types`.

## 4. Normalisasi Database

File migration aktif setelah pembersihan:

- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/2026_06_17_000002_create_document_types_table.php`
- `database/migrations/2026_06_17_000003_create_requirements_table.php`
- `database/migrations/2026_06_17_000004_create_student_applications_table.php`
- `database/migrations/2026_06_17_000005_create_application_documents_table.php`
- `database/migrations/2026_06_17_000006_create_announcements_table.php`
- `database/migrations/2026_06_25_000001_create_bookmarks_table.php`

Perubahan:

1. Kolom profil mahasiswa yang sebelumnya ditambahkan melalui migration susulan sekarang masuk langsung ke migration `users`.
2. Kolom `image_path` yang sebelumnya ditambahkan melalui migration terpisah sekarang masuk langsung ke migration `document_types`.
3. Migration lama yang hanya menjadi patch kompatibilitas atau no-op dihapus.
4. Tabel bawaan Laravel yang tidak dipakai oleh fitur aplikasi tidak lagi dibuat.
5. Foreign key, index, unique constraint, dan cascade dibuat lebih jelas pada relasi utama.
6. Tabel `bookmarks` diberi unique constraint `user_id` dan `document_type_id` agar satu mahasiswa tidak dapat menyimpan master yang sama berulang kali.

Tabel bawaan Laravel yang dihapus dari skema karena tidak digunakan oleh kode aplikasi:

- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

Konfigurasi pendukung juga disesuaikan agar tabel tersebut tidak dibutuhkan:

- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`
- `QUEUE_FAILED_DRIVER=null`

## 5. CSS dan Tampilan

File utama:

- `public/css/scholargate.css`
- `resources/css/app.css`
- `resources/js/app.js`
- `resources/js/bootstrap.js`

Perubahan:

1. Selector CSS yang tidak ditemukan pada Blade aplikasi dihapus.
2. Blok CSS duplikat pada area informasi dan bookmark dirapikan.
3. File CSS utama diberi header dokumentasi.
4. Class semantic yang dipakai sebagai penanda halaman tetap dipertahankan agar Blade tidak kehilangan hook desain.
5. File entry Vite diberi komentar bahwa UI utama memakai CSS custom di `public/css/scholargate.css`.

Jumlah baris CSS berubah dari sekitar 5.442 baris menjadi sekitar 4.577 baris setelah pembersihan.

## 6. Seeder dan Data Awal

File utama:

- `database/seeders/DatabaseSeeder.php`

Perubahan:

1. Seeder diberi dokumentasi.
2. Data admin, mahasiswa contoh, master beasiswa, syarat, dan pengumuman dipisahkan ke method khusus.
3. Seeder memakai `updateOrCreate` agar aman dijalankan lebih dari satu kali.
4. Seeder menggunakan constant role dari model `User`.

## 7. Dokumentasi Kode

File yang diberi dokumentasi tambahan meliputi:

1. Route aplikasi.
2. Middleware.
3. Controller.
4. Model.
5. Migration.
6. Seeder.
7. Entry JavaScript dan CSS.
8. Base test dan smoke test.
9. Layout utama Blade.

Dependency pihak ketiga di `vendor` dan `node_modules` tidak diedit karena file tersebut berasal dari package manager dan tidak boleh dimodifikasi manual.

## 8. File yang Dipertahankan dalam Paket Final

Untuk permintaan ini, paket final tetap menyertakan folder besar dan file proyek lengkap sebagaimana diminta, termasuk:

- `.env`
- `.git`
- `vendor`
- `node_modules`

Catatan: pada distribusi production atau pengiriman repository normal, folder dan file tersebut umumnya tidak ikut dikirim. Namun, pada paket ini semuanya dipertahankan agar struktur folder tidak berkurang dari ZIP awal.

## Hotfix Blade Comment Rendering
- Memperbaiki format komentar dokumentasi pada seluruh file Blade dari `{-- ... --}` menjadi `{{-- ... --}}` agar komentar tidak tampil sebagai teks di halaman.
- Perbaikan ini menghilangkan teks dokumentasi yang muncul di bagian atas halaman saat berpindah fitur.

## Hotfix MySQL Identifier Length
- Memperbaiki nama unique constraint pada tabel `application_documents` dari nama otomatis Laravel yang terlalu panjang menjadi `app_docs_app_req_unique`.
- Perbaikan ini mencegah error MySQL `SQLSTATE[42000] 1059 Identifier name is too long` saat menjalankan `php artisan migrate:fresh --seed`.
