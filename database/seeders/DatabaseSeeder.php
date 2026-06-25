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
                'ipk' => 3.50,
                'phone' => '081241456546',
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
                'name' => 'Surat Rekomendasi Beasiswa Clarendon',
                'category' => 'Prestasi Global',
                'provider' => 'Oxford Clarendon Scholarship',
                'description' => 'Pengajuan surat rekomendasi, validasi data akademik, dan kelengkapan berkas untuk kebutuhan pendaftaran beasiswa internasional.',
                'deadline' => now()->addMonths(2)->toDateString(),
                'registration_link' => 'https://example.com/clarendon',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Pernyataan Beasiswa Gates Cambridge',
                'category' => 'Kepemimpinan',
                'provider' => 'Gates Cambridge Grant',
                'description' => 'Layanan pengurusan surat pernyataan aktif kuliah dan rekomendasi prodi untuk pendaftaran beasiswa berbasis kepemimpinan.',
                'deadline' => now()->addMonth()->toDateString(),
                'registration_link' => 'https://example.com/gates',
                'is_active' => true,
            ],
            [
                'name' => 'Validasi Berkas DAAD Research Grant',
                'category' => 'Riset',
                'provider' => 'DAAD Research Grant',
                'description' => 'Pemeriksaan kelengkapan berkas akademik dan surat pengantar prodi untuk pendaftaran hibah riset internasional.',
                'deadline' => now()->addWeeks(6)->toDateString(),
                'registration_link' => 'https://example.com/daad',
                'is_active' => true,
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
