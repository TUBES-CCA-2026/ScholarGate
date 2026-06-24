# Panduan Cepat untuk Windows dan Laravel Herd

1. Buka folder kerja, misalnya `C:\Herd`.
2. Klik kanan, pilih `Open in Terminal`.
3. Jalankan:

```bash
composer create-project laravel/laravel scholargate
cd scholargate
```

4. Salin isi paket `scholargate_laravel_starter` ke folder project `scholargate`.
5. Buat database MySQL bernama `scholargate` melalui phpMyAdmin, TablePlus, HeidiSQL, atau database manager dari Herd.
6. Ubah `.env` sesuai database.
7. Jalankan:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

8. Login dengan akun demo.

```text
Admin: admin@prodi.test / password
Mahasiswa: mahasiswa@test.com / password
```
