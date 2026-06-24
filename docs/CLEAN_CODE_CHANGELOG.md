# Clean Code Changelog ScholarGate

## Perubahan Utama

### 1. Route Dibersihkan

File: `routes/web.php`

Sebelumnya route mahasiswa muncul dua kali. Sekarang route sudah dibagi menjadi empat kelompok:

- Public pages
- Authentication
- Student area
- Admin area

Dampak:

- Route lebih mudah dibaca.
- Risiko perubahan route ganda berkurang.
- Nama route tetap dipertahankan agar view lama tidak rusak.

### 2. Landing Page Tetap Digabungkan

File:

- `resources/views/landing.blade.php`
- `public/css/scholargate.css`
- `routes/web.php`

Route `/` tetap membuka landing page ScholarGate.

### 3. Controller Mahasiswa Dirapikan

File:

- `app/Http/Controllers/StudentDashboardController.php`
- `app/Http/Controllers/StudentApplicationController.php`

Perubahan:

- Query dashboard dipisahkan lebih rapi.
- Validasi pengajuan dipindahkan ke method `storeRules()`.
- Pembuatan kode pengajuan dipindahkan ke `generateApplicationCode()`.
- Pembuatan dokumen pengajuan dipindahkan ke `createApplicationDocument()`.
- Otorisasi view dan revisi dipisahkan ke method khusus.
- Penggantian file revisi dipisahkan ke `replaceDocumentFile()`.

### 4. Controller Admin Dirapikan

File:

- `app/Http/Controllers/Admin/AdminApplicationController.php`
- `app/Http/Controllers/Admin/AdminDashboardController.php`
- `app/Http/Controllers/Admin/AnnouncementController.php`
- `app/Http/Controllers/Admin/DocumentTypeController.php`

Perubahan:

- Status aplikasi dan dokumen dibuat lebih jelas.
- Filter pencarian admin dipisahkan ke method tersendiri.
- Logika penghapusan file gambar dipisahkan.
- Logika sinkronisasi syarat berkas tetap dipertahankan, tetapi dibuat lebih rapi.

### 5. Model Dirapikan

File:

- `app/Models/User.php`
- `app/Models/StudentApplication.php`
- `app/Models/ApplicationDocument.php`
- `app/Models/DocumentType.php`
- `app/Models/Requirement.php`
- `app/Models/Announcement.php`

Perubahan:

- Constant role ditambahkan di `User`.
- Constant status ditambahkan di `StudentApplication` dan `ApplicationDocument`.
- Relasi model tetap dipertahankan.
- Method `completionPercentage()` tetap dipertahankan dan dibuat lebih eksplisit.

### 6. Form Upload Pengajuan Diperbaiki

File: `resources/views/student/applications/create.blade.php`

Sebelumnya JavaScript hanya menampilkan daftar syarat dan checkbox manual. Sekarang setiap syarat juga membuat input file:

```html
<input type="file" name="requirement_files[ID]">
```

Dampak:

- Struktur view sekarang sesuai dengan controller `StudentApplicationController@store`.
- Mahasiswa dapat benar-benar mengunggah dokumen dari form pengajuan.

### 7. Migration Duplikat Dibuat Aman

File: `database/migrations/2026_06_18_091249_add_mahasiswa_fields_to_users_table.php`

Migration tersebut sebelumnya menambahkan kolom yang sudah dibuat oleh migration sebelumnya. Sekarang dibuat no-op agar `php artisan migrate:fresh` tidak gagal karena kolom duplikat.

### 8. Konfigurasi Contoh Disesuaikan

File: `.env.example`

Perubahan:

- `APP_NAME=ScholarGate`
- Locale menjadi Indonesia.
- Database contoh memakai MySQL `scholargate`.
- `FILESYSTEM_DISK=public` agar sesuai kebutuhan upload file.

## File yang Tidak Disertakan dalam ZIP Clean

- `.env`
- `.git`
- `vendor`
- `node_modules`

Alasan:

- `.env` berisi konfigurasi lokal dan berpotensi sensitif.
- `.git` tidak diperlukan untuk distribusi project.
- `vendor` dan `node_modules` dapat dibuat ulang dengan `composer install` dan `npm install`.

## Catatan Distribusi Clean

Paket clean ini tidak menyertakan `.env`, `.git`, `vendor`, `node_modules`, `database.sqlite`, dan file dump SQL. Struktur database dijalankan melalui migration Laravel.

## Update Desain Dashboard dan Landing Page

Perubahan tambahan:

1. Landing page dirapikan menjadi empat bagian utama: navigasi, hero, fitur inti, alur singkat, dan footer.
2. Bagian landing page yang terlalu dekoratif dihapus agar halaman lebih ringan dan fokus pada fungsi sistem.
3. Landing page dibuat lebih responsif untuk desktop, tablet, dan mobile.
4. Dashboard mahasiswa disesuaikan dengan desain landing page melalui hero gelap, badge akademik, kartu ringkasan, dan kartu beasiswa rounded.
5. Dashboard admin disesuaikan dengan gaya yang sama melalui hero gelap, panel statistik, dan tabel monitoring yang lebih modern.
6. Sidebar dan topbar dashboard diselaraskan dengan warna landing page, yaitu navy gelap, aksen biru, dan tombol kuning.
7. Styling tambahan ditempatkan pada `public/css/scholargate.css` agar identitas visual ScholarGate konsisten di landing page, dashboard mahasiswa, dan dashboard admin.

## Update Desain Login dan Register

Perubahan tambahan:

1. Halaman `login` dan `register` diselaraskan dengan landing page terbaru.
2. Navbar autentikasi memakai struktur visual yang sama dengan landing page, termasuk brand ScholarGate, tombol beranda, login, dan register.
3. Area utama dibuat sebagai hero card navy gelap seperti landing page, dengan aksen kuning dan biru.
4. Form login/register tetap memakai alur Laravel yang sama, sehingga route dan proses autentikasi tidak berubah.
5. Kartu form dibuat lebih clean, rounded, responsif, dan menyatu dengan desain dashboard/landing page.
6. Tombol submit memakai aksen kuning ScholarGate agar konsisten dengan CTA landing page.
7. Bagian dekoratif lama pada login/register diganti dengan alur ringkas yang lebih relevan terhadap sistem pengajuan beasiswa.

File yang diubah:

- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `public/css/scholargate.css`
