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
            ['email' => 'admin@umi.ac.id'],
            [
                'name' => 'Admin Prodi',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'phone' => '6281234567890',
                'photo_path' => 'profile-photos/admin-prodi.png',
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
                'description' => 'Beasiswa Unggulan adalah program bantuan pendidikan dari Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi (Kemendikbudristek) melalui Pusat Layanan Pembiayaan Pendidikan (Puslapdik). Program ini ditujukan bagi masyarakat Indonesia yang memiliki prestasi akademik maupun non-akademik untuk melanjutkan pendidikan ke jenjang lebih tinggi di dalam maupun luar negeri. Pada tahun 2025, Beasiswa Unggulan kembali dibuka dengan persyaratan dan tata cara pendaftaran yang perlu dipahami oleh calon pendaftar.',
                'deadline' => now()->addMonths(2)->toDateString(),
                'registration_link' => 'https://beasiswaunggulan.kemendikdasmen.go.id/',
                'is_active' => true,
                'image_path' => 'document-type-images/beasiswa-unggulan.jpg',
            ],
            [
                'name' => 'Beasiswa Djarum Plus',
                'category' => 'Kepemimpinan',
                'provider' => 'Djarum Foundation',
                'description' => 'Sejak 1984, Djarum Foundation berkomitmen memajukan pendidikan Indonesia melalui Djarum Beasiswa Plus, program beasiswa prestasi bagi mahasiswa berpotensi tinggi sebagai upaya menyiapkan masa depan bangsa yang lebih baik.',
                'deadline' => now()->addMonths(2)->toDateString(),
                'registration_link' => 'https://djarumbeasiswaplus.org/',
                'is_active' => true,
                'image_path' => 'document-type-images/beasiswa-djarum-plus.jpg',
            ],
            [
                'name' => 'BSI Scholarship',
                'category' => 'Unggulan',
                'provider' => 'Bank Syariah Indonesia',
                'description' => 'BSI Scholarship adalah program beasiswa dari Bank Syariah Indonesia dan BSI Maslahat yang menargetkan penyaluran kepada 5.250 pelajar dan mahasiswa pada tahun 2026.',
                'deadline' => now()->addMonths(2)->toDateString(),
                'registration_link' => 'https://www.bsischolarship.id/',
                'is_active' => true,
                'image_path' => 'document-type-images/beasiswa-bsi.jpg',
            ],
            [
                'name' => 'Beasiswa Bank Indonesia',
                'category' => 'S1',
                'provider' => 'Bank Indonesia',
                'description' => 'Beasiswa Bank Indonesia (BI) adalah program bantuan pendidikan yang diselenggarakan oleh Bank Indonesia untuk mendukung pengembangan generasi muda yang unggul dan kompetitif.',
                'deadline' => now()->addMonths(3)->toDateString(),
                'registration_link' => 'https://www.bsischolarship.id/',
                'is_active' => true,
                'image_path' => 'document-type-images/beasiswa-bi.jpg',
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
