# Dokumentasi Teknis ScholarGate

## 1. Ringkasan Aplikasi

ScholarGate adalah aplikasi Laravel untuk mengelola pengajuan berkas mahasiswa, terutama pengajuan beasiswa atau dokumen akademik ke admin program studi. Aplikasi memiliki dua aktor utama, yaitu mahasiswa dan admin.

Fitur utama ScholarGate meliputi landing page publik, login, register mahasiswa, dashboard mahasiswa, profil mahasiswa, informasi beasiswa, bookmark informasi, form pengajuan berkas, unggah dokumen, detail status pengajuan, revisi dokumen, dashboard admin, review pengajuan, validasi dokumen, pengelolaan master beasiswa, pengelolaan syarat berkas, dan pengelolaan pengumuman.

## 2. Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 12 |
| Bahasa | PHP 8.2+ |
| Frontend | Blade, CSS custom, JavaScript ringan |
| Database | MySQL sesuai `.env.example` |
| Upload file | Laravel Storage disk `public` |
| CSS utama | `public/css/scholargate.css` |
| Dependency PHP | `vendor` dari Composer |
| Dependency frontend | `node_modules` dari NPM |

## 3. Struktur Folder Penting

```text
app/Http/Controllers
├── AuthController.php
├── StudentDashboardController.php
├── StudentApplicationController.php
├── StudentBookmarkController.php
├── Auth/RegisterController.php
└── Admin/
    ├── AdminDashboardController.php
    ├── AdminApplicationController.php
    ├── AnnouncementController.php
    └── DocumentTypeController.php

app/Http/Middleware
├── EnsureAdmin.php
└── EnsureStudent.php

app/Models
├── User.php
├── DocumentType.php
├── Requirement.php
├── StudentApplication.php
├── ApplicationDocument.php
├── Announcement.php
└── Bookmark.php

database/migrations
├── 0001_01_01_000000_create_users_table.php
├── 2026_06_17_000002_create_document_types_table.php
├── 2026_06_17_000003_create_requirements_table.php
├── 2026_06_17_000004_create_student_applications_table.php
├── 2026_06_17_000005_create_application_documents_table.php
├── 2026_06_17_000006_create_announcements_table.php
└── 2026_06_25_000001_create_bookmarks_table.php

resources/views
├── landing.blade.php
├── auth/
├── student/
├── admin/
└── layouts/

public/css
└── scholargate.css
```

## 4. Alur Route

Route utama berada di `routes/web.php`. Struktur route dibagi menjadi empat area: publik, guest auth, mahasiswa, dan admin.

### 4.1 Route Publik

| URL | Nama Route | Fungsi |
|---|---|---|
| `/` | `landing` | Landing page ScholarGate |

### 4.2 Route Authentication

Route auth hanya dapat diakses oleh guest, kecuali logout yang membutuhkan user login.

| URL | Nama Route | Fungsi |
|---|---|---|
| `/login` | `login` | Menampilkan form login |
| `/login` POST | `login.store` | Memproses login |
| `/register` | `register` | Menampilkan form register mahasiswa |
| `/register` POST | `register.store` | Memproses register mahasiswa |
| `/logout` POST | `logout` | Logout user |

### 4.3 Route Mahasiswa

Semua route mahasiswa menggunakan middleware `auth` dan `student`.

| URL | Nama Route | Fungsi |
|---|---|---|
| `/home` | `student.home` | Dashboard mahasiswa |
| `/profile` | `student.profile` | Profil mahasiswa |
| `/profile/edit` | `student.profile.edit` | Form edit profil |
| `/profile` PUT | `student.profile.update` | Update profil |
| `/information` | `student.information` | Informasi jenis pengajuan atau beasiswa |
| `/bookmarks` | `student.bookmarks.index` | Daftar informasi yang disimpan mahasiswa |
| `/bookmarks` POST | `student.bookmarks.store` | Menambah bookmark |
| `/bookmarks/{documentType}` DELETE | `student.bookmarks.destroy` | Menghapus bookmark |
| `/analytics` | `student.analytics` | Ringkasan statistik pengajuan |
| `/applications` | `student.applications.index` | Daftar pengajuan mahasiswa |
| `/applications/create` | `student.applications.create` | Form pengajuan baru |
| `/applications` POST | `student.applications.store` | Simpan pengajuan baru |
| `/applications/{studentApplication}` | `student.applications.show` | Detail pengajuan |
| `/applications/{studentApplication}/documents/{applicationDocument}/revision` PATCH | `student.applications.documents.revise` | Upload revisi dokumen |

### 4.4 Route Admin

Semua route admin menggunakan middleware `auth` dan `admin`.

| URL | Nama Route | Fungsi |
|---|---|---|
| `/admin/dashboard` | `admin.dashboard` | Dashboard admin |
| `/admin/applications` | `admin.applications.index` | Daftar pengajuan mahasiswa |
| `/admin/applications/{studentApplication}` | `admin.applications.show` | Detail review pengajuan |
| `/admin/applications/{studentApplication}/status` PATCH | `admin.applications.update-status` | Update status pengajuan |
| `/admin/applications/{studentApplication}/documents/{applicationDocument}` PATCH | `admin.applications.documents.update` | Update status dokumen |
| `/admin/document-types` | `admin.document-types.index` | Kelola jenis pengajuan |
| `/admin/document-types` POST | `admin.document-types.store` | Tambah jenis pengajuan |
| `/admin/document-types/{documentType}` PUT | `admin.document-types.update` | Update jenis pengajuan |
| `/admin/document-types/{documentType}` DELETE | `admin.document-types.destroy` | Hapus jenis pengajuan |
| `/admin/announcements` | `admin.announcements.index` | Kelola pengumuman |
| `/admin/announcements` POST | `admin.announcements.store` | Tambah pengumuman |
| `/admin/announcements/{announcement}` DELETE | `admin.announcements.destroy` | Hapus pengumuman |

## 5. Struktur Database Normal

Database aplikasi difokuskan pada tujuh tabel domain yang dipakai langsung oleh kode.

### 5.1 `users`

Menyimpan akun mahasiswa dan admin. Kolom profil mahasiswa seperti `nim`, `program_studi`, `kelas`, `ipk`, `phone`, dan `photo_path` berada langsung pada migration utama `users` agar tidak bergantung pada migration patch.

Relasi utama:

- Satu user memiliki banyak `student_applications`.
- Satu user memiliki banyak `bookmarks`.

### 5.2 `document_types`

Menyimpan master beasiswa atau jenis pengajuan. Kolom `image_path` berada langsung pada migration utama tabel ini.

Relasi utama:

- Satu `document_type` memiliki banyak `requirements`.
- Satu `document_type` memiliki banyak `student_applications`.
- Satu `document_type` dapat disimpan oleh banyak user melalui `bookmarks`.

### 5.3 `requirements`

Menyimpan syarat dokumen yang terhubung ke satu master beasiswa. Tabel ini menggantikan pendekatan teks bebas agar setiap syarat dapat dilacak, divalidasi, dan dipakai dalam detail pengajuan.

Relasi utama:

- Satu `requirement` milik satu `document_type`.
- Satu `requirement` dapat memiliki banyak `application_documents`.

### 5.4 `student_applications`

Menyimpan header pengajuan mahasiswa. Setiap pengajuan selalu terhubung ke satu user dan satu master beasiswa.

Status pengajuan:

| Status | Label |
|---|---|
| `submitted` | Dikirim |
| `in_review` | Sedang Direview |
| `revision` | Perlu Revisi |
| `approved` | Disetujui |
| `rejected` | Ditolak |
| `completed` | Selesai |

### 5.5 `application_documents`

Menyimpan detail dokumen per syarat. Tabel ini menghubungkan pengajuan mahasiswa dengan requirement yang harus dipenuhi.

Status dokumen:

| Status | Label |
|---|---|
| `missing` | Belum Ada |
| `submitted` | Dikirim |
| `valid` | Valid |
| `invalid` | Perlu Revisi |

### 5.6 `announcements`

Menyimpan pengumuman yang tampil pada area informasi mahasiswa.

### 5.7 `bookmarks`

Menyimpan informasi beasiswa yang ditandai oleh mahasiswa. Tabel ini memiliki unique constraint pada pasangan `user_id` dan `document_type_id` agar data tidak duplikat.

## 6. Tabel yang Dihapus dari Skema

Tabel berikut tidak lagi dibuat karena tidak digunakan oleh fitur ScholarGate dan konfigurasi sudah diarahkan agar tidak memerlukan tabel database bawaan tersebut:

- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

Konfigurasi yang mendukung pembersihan ini:

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
QUEUE_FAILED_DRIVER=null
```

## 7. Alur Bisnis Mahasiswa

Mahasiswa membuka landing page, melakukan register atau login, melihat informasi beasiswa, menyimpan bookmark jika diperlukan, memilih jenis pengajuan, mengisi tujuan, mengunggah dokumen sesuai syarat, lalu mengirim pengajuan. Sistem membuat kode pengajuan otomatis dan memberi status awal `submitted`.

Setelah admin memeriksa dokumen, mahasiswa dapat melihat status detail. Jika dokumen diberi status `invalid` atau `missing` dan pengajuan berada pada status `revision`, mahasiswa dapat mengunggah file revisi melalui halaman detail pengajuan.

## 8. Alur Bisnis Admin

Admin login, masuk ke dashboard admin, melihat daftar pengajuan, membuka detail pengajuan, memeriksa setiap dokumen, lalu mengubah status dokumen menjadi `valid`, `invalid`, `missing`, atau `submitted`. Jika terdapat dokumen `invalid`, status pengajuan dapat diarahkan ke `revision`. Admin juga dapat mengubah status utama pengajuan menjadi `approved`, `rejected`, atau `completed` sesuai hasil verifikasi.

Admin memiliki akses untuk mengelola master beasiswa, foto master, syarat dokumen, status aktif master, dan pengumuman.

## 9. CSS dan Asset

CSS utama berada di `public/css/scholargate.css`. File ini sudah dibersihkan dari selector yang tidak dipakai oleh Blade aplikasi. Entry Vite pada `resources/css/app.css`, `resources/js/app.js`, dan `resources/js/bootstrap.js` tetap dipertahankan karena masih menjadi struktur standar Laravel untuk pengembangan frontend.

Dependency pihak ketiga pada `vendor` dan `node_modules` tidak diedit manual.

## 10. Cara Menjalankan Project

1. Ekstrak ZIP project.
2. Masuk ke folder project.
3. Pastikan dependency sudah ada. Paket final ini menyertakan `vendor` dan `node_modules`, tetapi perintah berikut tetap dapat dijalankan ulang bila diperlukan:

```bash
composer install
npm install
```

4. Sesuaikan konfigurasi database di `.env`.
5. Buat database MySQL:

```sql
CREATE DATABASE scholargate;
```

6. Jalankan migration:

```bash
php artisan migrate
```

7. Buat storage link:

```bash
php artisan storage:link
```

8. Jalankan server lokal:

```bash
php artisan serve
```

9. Buka aplikasi pada browser:

```text
http://127.0.0.1:8000
```

## 11. File yang Umumnya Diedit

| Kebutuhan Edit | File Utama |
|---|---|
| Mengubah landing page | `resources/views/landing.blade.php` |
| Mengubah warna atau desain | `public/css/scholargate.css` |
| Mengubah route | `routes/web.php` |
| Mengubah hak akses role | `app/Http/Middleware/EnsureAdmin.php`, `app/Http/Middleware/EnsureStudent.php` |
| Mengubah dashboard mahasiswa | `app/Http/Controllers/StudentDashboardController.php`, `resources/views/student/home.blade.php` |
| Mengubah pengajuan mahasiswa | `app/Http/Controllers/StudentApplicationController.php` |
| Mengubah bookmark | `app/Http/Controllers/StudentBookmarkController.php`, `app/Models/Bookmark.php` |
| Mengubah review admin | `app/Http/Controllers/Admin/AdminApplicationController.php` |
| Mengubah master beasiswa | `app/Http/Controllers/Admin/DocumentTypeController.php` |
| Mengubah relasi data | `app/Models/*.php`, `database/migrations/*.php` |

## 12. Catatan Keamanan

Paket final ini tetap menyertakan `.env`, `.git`, `vendor`, dan `node_modules` karena diminta agar semua folder dari ZIP awal tetap lengkap. Untuk distribusi production atau repository normal, `.env`, `.git`, `vendor`, dan `node_modules` biasanya tidak dibagikan. File `.env` berisi konfigurasi lokal dan sebaiknya tidak dikirim ke pihak yang tidak berkepentingan.

Validasi file upload tetap berada di controller. Storage public tetap memakai disk `public`, sehingga `php artisan storage:link` diperlukan agar file upload dapat diakses melalui browser.

## 13. Catatan Pengembangan Lanjutan

Pengembangan berikutnya yang masih dapat dilakukan adalah memindahkan validasi besar ke Form Request, menambah Policy Laravel untuk otorisasi pengajuan, menambah test untuk proses pengajuan dan revisi dokumen, menambah notifikasi email atau WhatsApp yang lebih terstruktur, serta membuat dashboard statistik admin berbasis chart.
