<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder awal untuk kebutuhan demo ScholarGate.
 *
 * Data dibuat idempotent melalui updateOrCreate agar aman dijalankan ulang
 * tanpa menghasilkan akun atau master beasiswa ganda.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan seluruh proses seed data awal aplikasi.
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedDocumentTypes();
        $this->seedAnnouncements();
    }

    /**
     * Membuat akun demo admin dan mahasiswa.
     */
    private function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@prodi.test'],
            [
                'name' => 'Admin Prodi',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'phone' => '6281234567890',
                'photo_path' => 'profile-photos/admin-prodi.jpg',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@test.com'],
            [
                'name' => 'Karima',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
                'nim' => '13020250001',
                'program_studi' => 'Sistem Informasi',
                'kelas' => 'A1',
                'ipk' => 3.80,
                'phone' => '6289876543210',
            ]
        );

        User::updateOrCreate(
            ['email' => 'refazym@gmail.com'],
            [
                'name' => 'Rendi Pratama',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
                'nim' => '13020240184',
                'program_studi' => 'Teknik Informatika',
                'kelas' => 'A5',
                'ipk' => 3.72,
                'phone' => '081241456546',
                'photo_path' => 'profile-photos/rendi-pratama.jpg',
            ]
        );

        User::updateOrCreate(
            ['email' => 'nabil@gmail.com'],
            [
                'name' => 'Nabil',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
                'nim' => '13020240060',
                'program_studi' => 'Teknik Informatika',
                'kelas' => 'A5',
                'ipk' => 3.72,
                'phone' => '081241456546',
                'photo_path' => 'profile-photos/nabil.jpg',
            ]
        );
    }

    /**
     * Membuat master beasiswa beserta syarat dokumen dasarnya.
     */
    private function seedDocumentTypes(): void
    {
        $documentTypes = [
            [
                'name' => 'Beasiswa Unggulan',
                'category' => 'S1',
                'provider' => 'Kemendikdasmen',
                'description' => 'Program bantuan biaya pendidikan dari pemerintah Indonesia bagi mahasiswa berprestasi pada jenjang S1, S2, dan S3, dengan seleksi berbasis kelengkapan dokumen, prestasi, rekomendasi, dan kelayakan akademik.',
                'deadline' => now()->addMonths(2)->toDateString(),
                'registration_link' => 'https://beasiswaunggulan.kemendikdasmen.go.id/',
                'is_active' => true,
                'image_path' => 'document-types/beasiswa-unggulan.webp',
            ],
            [
                'name' => 'Beasiswa Djarum Plus',
                'category' => 'Kepemimpinan',
                'provider' => 'Djarum Foundation',
                'description' => 'Program beasiswa bagi mahasiswa berprestasi yang tidak hanya memberikan bantuan dana pendidikan, tetapi juga pelatihan soft skills, kepemimpinan, wawasan kebangsaan, karakter, dan jejaring alumni.',
                'deadline' => now()->addMonth(1)->toDateString(),
                'registration_link' => 'https://djarumbeasiswaplus.org/',
                'is_active' => true,
                'image_path' => 'document-types/beasiswa-djarum-plus.png',
            ],
            [
                'name' => 'Beasiswa Kalla ',
                'category' => 'S1',
                'provider' => 'Yayasan Hadji Kalla',
                'description' => 'Pemeriksaan kelengkapan berkas akademik dan surat pengantar prodi untuk pendaftaran hibah riset internasional.',
                'deadline' => now()->addWeeks(6)->toDateString(),
                'registration_link' => 'https://www.yayasanhadjikalla.or.id/educare/beasiswa-kalla/',
                'is_active' => true,
                'image_path' => 'document-types/beasiswa-kalla.png',
            ],
            [
                'name' => 'Beasiswa Bank Indonesia',
                'category' => 'S1',
                'provider' => 'Bank Indonesia',
                'description' => 'Program beasiswa bagi mahasiswa S1 dari perguruan tinggi terpilih yang memberikan bantuan biaya pendidikan, tunjangan studi, biaya hidup, serta pembinaan melalui komunitas Generasi Baru Indonesia.',
                'deadline' => now()->addMonths(2)->toDateString(),
                'registration_link' => 'https://www.generasibaruindonesia.com/beasiswa',
                'is_active' => true,
                'image_path' => 'document-types/beasiswa-bank-indonesia.webp',
            ],
        ];

        foreach ($documentTypes as $payload) {
            $type = DocumentType::updateOrCreate(['name' => $payload['name']], $payload);
            $this->seedRequirements($type);
        }
    }

    /**
     * Membuat syarat standar untuk satu master beasiswa.
     */
    private function seedRequirements(DocumentType $type): void
    {
        $requirements = [
            ['name' => 'Kartu Tanda Mahasiswa', 'description' => 'Unggah file KTM dalam format PDF, JPG, atau PNG.', 'is_required' => true, 'needs_file' => true],
            ['name' => 'Transkrip Nilai Terbaru', 'description' => 'Unggah transkrip nilai terbaru.', 'is_required' => true, 'needs_file' => true, 'has_expiry' => true, 'valid_days' => 90],
            ['name' => 'Draf Surat Pernyataan', 'description' => 'Unggah draf surat atau centang jika akan diproses manual.', 'is_required' => true, 'needs_file' => true],
        ];

        foreach ($requirements as $requirement) {
            $type->requirements()->updateOrCreate(
                ['name' => $requirement['name']],
                $requirement
            );
        }
    }

    /**
     * Membuat pengumuman demo untuk halaman informasi mahasiswa.
     */
    private function seedAnnouncements(): void
    {
        Announcement::updateOrCreate(
            ['title' => 'Layanan pengajuan berkas prodi dibuka'],
            [
                'body' => 'Mahasiswa dapat mengajukan validasi berkas, surat pernyataan, dan surat rekomendasi melalui menu Pengajuan.',
                'published_at' => now(),
            ]
        );
    }
}
