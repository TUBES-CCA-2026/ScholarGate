# Dokumentasi Teknis ScholarGate

## 1. Ringkasan Aplikasi

ScholarGate adalah aplikasi Laravel untuk mengelola pengajuan berkas mahasiswa, khususnya pengajuan beasiswa atau dokumen akademik ke admin program studi. Aplikasi memiliki dua aktor utama, yaitu mahasiswa dan admin prodi.

Fitur utama:

- Landing page publik untuk pengenalan sistem.
- Login dan register mahasiswa.
- Dashboard mahasiswa.
- Profil mahasiswa dan edit profil.
- Informasi beasiswa atau jenis pengajuan.
- Form pengajuan berkas dengan unggah dokumen.
- Detail status pengajuan mahasiswa.
- Revisi dokumen yang ditolak atau belum lengkap.
- Dashboard admin.
- Review pengajuan mahasiswa.
- Validasi status dokumen.
- Pengelolaan master jenis pengajuan dan syarat berkas.
- Pengelolaan pengumuman.

## 2. Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 12 |
| Bahasa | PHP 8.2+ |
| Frontend | Blade, CSS custom, JavaScript ringan |
| Database | MySQL sesuai `.env.example` |
| Upload file | Laravel Storage disk `public` |
| CSS utama | `public/css/scholargate.css` |

## 3. Struktur Folder Penting

```text
app/Http/Controllers
├── AuthController.php
├── StudentDashboardController.php
├── StudentApplicationController.php
├── Auth/RegisterController.php
└── Admin/
    ├── AdminDashboardController.php
    ├── AdminApplicationController.php
    ├── AnnouncementController.php
    └── DocumentTypeController.php

app/Models
├── User.php
├── DocumentType.php
├── Requirement.php
├── StudentApplication.php
├── ApplicationDocument.php
└── Announcement.php

resources/views
├── landing.blade.php
├── auth/
├── student/
├── admin/
└── layouts/

routes
└── web.php

public/css
└── scholargate.css
```

## 4. Alur Route

### 4.1 Route Publik

| URL | Nama Route | Fungsi |
|---|---|---|
| `/` | `landing` | Landing page ScholarGate |

### 4.2 Route Authentication

| URL | Nama Route | Fungsi |
|---|---|---|
| `/login` | `login` | Menampilkan form login |
| `/login` POST | `login.store` | Memproses login |
| `/register` | `register` | Menampilkan form register |
| `/register` POST | `register.store` | Memproses register mahasiswa |
| `/logout` POST | `logout` | Logout user |

### 4.3 Route Mahasiswa

| URL | Nama Route | Fungsi |
|---|---|---|
| `/home` | `student.home` | Dashboard mahasiswa |
| `/profile` | `student.profile` | Profil mahasiswa |
| `/profile/edit` | `student.profile.edit` | Form edit profil |
| `/profile` PUT | `student.profile.update` | Update profil |
| `/information` | `student.information` | Informasi jenis pengajuan/beasiswa |
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

## 5. Struktur Database dan Relasi

### 5.1 `users`

Menyimpan akun mahasiswa dan admin.

Kolom penting:

- `name`
- `email`
- `password`
- `role`
- `nim`
- `program_studi`
- `kelas`
- `ipk`
- `phone`
- `photo_path`

Relasi:

- Satu `User` memiliki banyak `StudentApplication`.

### 5.2 `document_types`

Menyimpan master jenis pengajuan atau beasiswa.

Kolom penting:

- `name`
- `category`
- `provider`
- `description`
- `image_path`
- `deadline`
- `registration_link`
- `is_active`

Relasi:

- Satu `DocumentType` memiliki banyak `Requirement`.
- Satu `DocumentType` memiliki banyak `StudentApplication`.

### 5.3 `requirements`

Menyimpan syarat dokumen untuk setiap jenis pengajuan.

Kolom penting:

- `document_type_id`
- `name`
- `description`
- `is_required`
- `needs_file`
- `has_expiry`
- `valid_days`

Relasi:

- Satu `Requirement` milik satu `DocumentType`.
- Satu `Requirement` dapat digunakan banyak `ApplicationDocument`.

### 5.4 `student_applications`

Menyimpan data pengajuan mahasiswa.

Kolom penting:

- `user_id`
- `document_type_id`
- `application_code`
- `purpose`
- `status`
- `admin_note`
- `submitted_at`

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

Menyimpan dokumen yang dikirim mahasiswa untuk memenuhi syarat pengajuan.

Kolom penting:

- `student_application_id`
- `requirement_id`
- `file_path`
- `original_name`
- `is_checked_manual`
- `expired_at`
- `status`
- `note`

Status dokumen:

| Status | Label |
|---|---|
| `missing` | Belum Ada |
| `submitted` | Dikirim |
| `valid` | Valid |
| `invalid` | Perlu Revisi |

### 5.6 `announcements`

Menyimpan pengumuman yang tampil pada area informasi mahasiswa.

Kolom penting:

- `title`
- `body`
- `published_at`

## 6. Alur Bisnis Aplikasi

### 6.1 Alur Mahasiswa

1. Mahasiswa membuka landing page.
2. Mahasiswa melakukan register atau login.
3. Mahasiswa melihat informasi beasiswa atau jenis pengajuan.
4. Mahasiswa memilih jenis pengajuan.
5. Sistem menampilkan syarat berkas sesuai master data.
6. Mahasiswa mengunggah dokumen atau mencentang opsi proses manual.
7. Sistem membuat kode pengajuan otomatis.
8. Status awal pengajuan menjadi `submitted`.
9. Mahasiswa dapat melihat detail dan status pengajuan.
10. Jika admin menandai dokumen `invalid` atau `missing`, mahasiswa dapat mengunggah revisi.

### 6.2 Alur Admin

1. Admin login.
2. Admin masuk ke dashboard admin.
3. Admin melihat daftar pengajuan mahasiswa.
4. Admin membuka detail pengajuan.
5. Admin memeriksa dokumen satu per satu.
6. Admin mengubah status dokumen menjadi `valid`, `invalid`, `missing`, atau `submitted`.
7. Jika ada dokumen `invalid`, status pengajuan otomatis menjadi `revision`.
8. Admin dapat mengubah status pengajuan utama menjadi `approved`, `rejected`, atau `completed`.
9. Admin dapat mengelola master jenis pengajuan, syarat berkas, dan pengumuman.

## 7. Landing Page

Landing page berada di:

```text
resources/views/landing.blade.php
```

CSS pendukung berada di:

```text
public/css/scholargate.css
```

Route landing page berada di:

```php
Route::view('/', 'landing')->name('landing');
```

Desain landing page dibuat konsisten dengan halaman login dan register, yaitu memakai gaya visual navy, blue, yellow accent, kartu rounded, badge, icon akademik, dan CTA login/register.

## 8. Catatan Clean Code

Perubahan clean code yang sudah diterapkan:

1. Route mahasiswa yang sebelumnya dobel sudah disatukan.
2. Route publik, auth, mahasiswa, dan admin sudah dipisahkan secara logis.
3. Controller dirapikan dengan return type eksplisit seperti `View` dan `RedirectResponse`.
4. Validasi dipindahkan ke method khusus pada controller yang kompleks.
5. Business logic kecil dipisahkan menjadi private method.
6. Status role dan status dokumen dibuat lebih konsisten melalui constant model.
7. Migration duplikat mahasiswa dibuat no-op agar `migrate:fresh` tidak gagal.
8. Form pengajuan diperbaiki agar benar-benar membuat input upload dokumen sesuai struktur controller.
9. `.env` tidak ikut disertakan dalam paket clean karena berisi konfigurasi lokal yang sensitif.
10. `.env.example` disesuaikan untuk konfigurasi ScholarGate.

## 9. Cara Menjalankan Project

1. Ekstrak ZIP project.
2. Masuk ke folder project.
3. Jalankan:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
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

## 10. File yang Umumnya Diedit

| Kebutuhan Edit | File Utama |
|---|---|
| Mengubah landing page | `resources/views/landing.blade.php` |
| Mengubah warna/desain | `public/css/scholargate.css` |
| Mengubah route | `routes/web.php` |
| Mengubah dashboard mahasiswa | `app/Http/Controllers/StudentDashboardController.php`, `resources/views/student/home.blade.php` |
| Mengubah pengajuan mahasiswa | `app/Http/Controllers/StudentApplicationController.php` |
| Mengubah review admin | `app/Http/Controllers/Admin/AdminApplicationController.php` |
| Mengubah master beasiswa | `app/Http/Controllers/Admin/DocumentTypeController.php` |
| Mengubah relasi data | `app/Models/*.php` dan `database/migrations/*.php` |

## 11. Catatan Keamanan

- Jangan membagikan file `.env`.
- Jangan commit folder `vendor`, `node_modules`, dan `.git` ke ZIP distribusi.
- Gunakan validasi file upload seperti yang sudah diterapkan pada controller.
- Jalankan `php artisan storage:link` agar file upload bisa diakses dari folder `storage/app/public`.
- Pastikan role admin hanya diberikan pada akun yang benar.

## 12. Catatan Pengembangan Lanjutan

Beberapa pengembangan yang masih dapat dilakukan:

- Membuat seeder admin default.
- Membuat Form Request terpisah untuk validasi yang lebih modular.
- Menambah policy Laravel untuk otorisasi pengajuan.
- Menambah unit test untuk proses pengajuan dan revisi dokumen.
- Menambah fitur notifikasi email atau WhatsApp yang lebih terstruktur.
- Menambah dashboard statistik admin berbasis chart.

## Catatan Distribusi Clean

Paket clean ini tidak menyertakan `.env`, `.git`, `vendor`, `node_modules`, `database.sqlite`, dan file dump SQL. Struktur database dijalankan melalui migration Laravel.

## Pembaruan Desain Landing Page dan Dashboard

Desain terbaru ScholarGate menggunakan bahasa visual yang konsisten antara halaman publik dan halaman dashboard. Landing page dibuat lebih ringkas dengan bagian utama berikut:

1. Navigasi utama.
2. Hero section sebagai pengantar sistem.
3. Fitur utama untuk mahasiswa dan admin.
4. Alur singkat penggunaan sistem.
5. Footer sederhana.

Dashboard mahasiswa dan admin menggunakan gaya visual yang sama dengan landing page, yaitu latar navy gelap pada hero, aksen kuning untuk tombol utama, kartu putih rounded, dan panel ringkasan berbasis grid. Perubahan ini dilakukan agar pengguna tidak merasa berpindah ke sistem yang berbeda setelah login.

File utama yang berkaitan dengan pembaruan desain:

- `resources/views/landing.blade.php`
- `resources/views/student/home.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `public/css/scholargate.css`

## Desain Halaman Login dan Register

Halaman login dan register menggunakan desain yang sama dengan landing page ScholarGate. Struktur visualnya terdiri atas navbar putih, hero card navy gelap, aksen kuning sebagai tombol utama, dan kartu form putih rounded. Perubahan ini hanya menyentuh tampilan Blade dan CSS, sementara route autentikasi, controller, validasi, serta proses login/register tetap menggunakan alur Laravel yang sama.

File terkait:

- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `public/css/scholargate.css`
