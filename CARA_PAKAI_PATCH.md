# Patch Bahasa Indonesia ScholarGate

Patch ini mengganti teks antarmuka website menjadi bahasa Indonesia, memperbaiki error Blade pada halaman pengajuan, menambahkan label status bahasa Indonesia, dan memasang format pesan WhatsApp pengajuan berkas.

## Cara pasang

1. Ekstrak file ZIP ini.
2. Salin folder `resources`, `app`, `database`, dan `lang` ke folder project Laravel Anda.
3. Pilih Replace atau Timpa file jika diminta.
4. Tambahkan atau ubah baris ini di file `.env`:

```env
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
```

5. Jalankan perintah berikut:

```bash
php artisan optimize:clear
php artisan view:clear
```

6. Jika ingin data contoh di database ikut berubah menjadi bahasa Indonesia, jalankan:

```bash
php artisan migrate:fresh --seed
```

Catatan: perintah `migrate:fresh --seed` akan menghapus data lama dan membuat ulang data contoh.
