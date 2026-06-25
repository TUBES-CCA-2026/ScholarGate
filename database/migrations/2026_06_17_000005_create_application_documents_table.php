<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel detail dokumen pengajuan.
     *
     * Setiap baris menghubungkan satu pengajuan dengan satu syarat berkas.
     * Constraint unik menjaga agar satu syarat tidak masuk dua kali dalam satu
     * pengajuan yang sama.
     */
    public function up(): void
    {
        Schema::create('application_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requirement_id')->constrained()->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->boolean('is_checked_manual')->default(false);
            $table->date('expired_at')->nullable()->index();
            $table->string('status', 30)->default('missing')->index();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['student_application_id', 'requirement_id'], 'app_docs_app_req_unique');
        });
    }

    /**
     * Menghapus tabel detail dokumen pengajuan.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};
