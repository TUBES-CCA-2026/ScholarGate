<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@prodi.test'],
            [
                'name' => 'Admin Prodi',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '6281234567890',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@test.com'],
            [
                'name' => 'Karima',
                'password' => Hash::make('password'),
                'role' => 'student',
                'nim' => '13020250001',
                'program_studi' => 'Sistem Informasi',
                'kelas' => 'A1',
                'ipk' => 3.80,
                'phone' => '6289876543210',
            ]
        );

        $clarendon = DocumentType::create([
            'name' => 'Surat Rekomendasi Beasiswa Clarendon',
            'category' => 'Prestasi Global',
            'provider' => 'Oxford Clarendon Scholarship',
            'description' => 'Pengajuan surat rekomendasi, validasi data akademik, dan kelengkapan berkas untuk kebutuhan pendaftaran beasiswa internasional.',
            'deadline' => now()->addMonths(2)->toDateString(),
            'registration_link' => 'https://example.com/clarendon',
            'is_active' => true,
        ]);

        $gates = DocumentType::create([
            'name' => 'Surat Pernyataan Beasiswa Gates Cambridge',
            'category' => 'Kepemimpinan',
            'provider' => 'Gates Cambridge Grant',
            'description' => 'Layanan pengurusan surat pernyataan aktif kuliah dan rekomendasi prodi untuk pendaftaran beasiswa berbasis kepemimpinan.',
            'deadline' => now()->addMonth()->toDateString(),
            'registration_link' => 'https://example.com/gates',
            'is_active' => true,
        ]);

        $daad = DocumentType::create([
            'name' => 'Validasi Berkas DAAD Research Grant',
            'category' => 'Riset',
            'provider' => 'DAAD Research Grant',
            'description' => 'Pemeriksaan kelengkapan berkas akademik dan surat pengantar prodi untuk pendaftaran hibah riset internasional.',
            'deadline' => now()->addWeeks(6)->toDateString(),
            'registration_link' => 'https://example.com/daad',
            'is_active' => true,
        ]);

        foreach ([$clarendon, $gates, $daad] as $type) {
            $type->requirements()->createMany([
                ['name' => 'Kartu Tanda Mahasiswa', 'description' => 'Unggah file KTM dalam format PDF, JPG, atau PNG.', 'is_required' => true, 'needs_file' => true],
                ['name' => 'Transkrip Nilai Terbaru', 'description' => 'Unggah transkrip nilai terbaru.', 'is_required' => true, 'needs_file' => true, 'has_expiry' => true, 'valid_days' => 90],
                ['name' => 'Draf Surat Pernyataan', 'description' => 'Unggah draf surat atau centang jika akan diproses manual.', 'is_required' => true, 'needs_file' => true],
            ]);
        }

        Announcement::create([
            'title' => 'Layanan pengajuan berkas prodi dibuka',
            'body' => 'Mahasiswa dapat mengajukan validasi berkas, surat pernyataan, dan surat rekomendasi melalui menu Pengajuan.',
            'published_at' => now(),
        ]);
    }
}
