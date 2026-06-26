<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom deleted_at agar pengajuan bisa masuk recycle bin
     * tanpa langsung hilang permanen dari database.
     */
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table): void {
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Menghapus kolom deleted_at jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
