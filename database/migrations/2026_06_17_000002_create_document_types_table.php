<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat master jenis pengajuan atau informasi beasiswa.
     *
     * Satu baris merepresentasikan satu layanan/beasiswa. Syarat berkas tidak
     * disimpan di tabel ini karena memiliki kardinalitas one-to-many dan sudah
     * dinormalisasi ke tabel requirements.
     */
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('category', 100)->default('Beasiswa')->index();
            $table->string('provider')->nullable();
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->date('deadline')->nullable()->index();
            $table->string('registration_link', 2048)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Menghapus master jenis pengajuan.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
