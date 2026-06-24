<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Compatibility migration.
     *
     * Field mahasiswa sudah dikelola oleh migration:
     * 2026_06_17_000001_add_role_and_profile_fields_to_users_table.php.
     * File ini sengaja dibuat no-op agar riwayat migration lama tetap aman
     * dan migrate:fresh tidak gagal karena kolom users yang dobel.
     */
    public function up(): void
    {
        // No operation.
    }

    public function down(): void
    {
        // No operation.
    }
};
