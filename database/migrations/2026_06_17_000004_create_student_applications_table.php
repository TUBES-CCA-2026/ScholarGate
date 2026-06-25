<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel header pengajuan mahasiswa.
     *
     * Detail dokumen tidak disimpan di sini, tetapi pada application_documents
     * agar struktur tetap normal dan satu pengajuan dapat memiliki banyak
     * syarat berkas.
     */
    public function up(): void
    {
        Schema::create('student_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->string('application_code')->unique();
            $table->text('purpose');
            $table->string('status', 30)->default('submitted')->index();
            $table->text('admin_note')->nullable();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel header pengajuan mahasiswa.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};
