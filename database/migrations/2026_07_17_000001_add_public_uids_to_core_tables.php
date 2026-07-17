<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Menambahkan UID publik tanpa mengganti primary key integer yang sudah ada.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'document_types',
            'student_applications',
            'application_documents',
            'announcements',
        ];

        /*
         * Tambahkan kolom UID.
         *
         * Dibuat nullable terlebih dahulu agar aman untuk tabel yang sudah
         * memiliki data lama. Seluruh data lama akan diisi pada proses berikutnya.
         */
        foreach ($tables as $tableName) {
            if (! Schema::hasColumn($tableName, 'uid')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table
                        ->uuid('uid')
                        ->nullable()
                        ->unique()
                        ->after('id');
                });
            }
        }

        /*
         * Mengisi UID untuk seluruh data lama.
         */
        foreach ($tables as $tableName) {
            DB::table($tableName)
                ->whereNull('uid')
                ->orderBy('id')
                ->chunkById(100, function ($rows) use ($tableName): void {
                    foreach ($rows as $row) {
                        DB::table($tableName)
                            ->where('id', $row->id)
                            ->update([
                                'uid' => (string) Str::uuid7(),
                            ]);
                    }
                });
        }
    }

    /**
     * Menghapus UID ketika migration di-rollback.
     */
    public function down(): void
    {
        $tables = [
            'application_documents',
            'student_applications',
            'announcements',
            'document_types',
            'users',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'uid')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropUnique(['uid']);
                    $table->dropColumn('uid');
                });
            }
        }
    }
};