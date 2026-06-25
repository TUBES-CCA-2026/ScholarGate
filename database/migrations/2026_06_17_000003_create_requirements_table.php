<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel requirements untuk daftar syarat setiap master pengajuan.
     *
     * Relasi ini memisahkan data berulang dari document_types. Constraint unik
     * mencegah nama syarat yang sama terduplikasi pada satu master beasiswa.
     */
    public function up(): void
    {
        Schema::create('requirements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('needs_file')->default(true);
            $table->boolean('has_expiry')->default(false);
            $table->unsignedInteger('valid_days')->nullable();
            $table->timestamps();

            $table->unique(['document_type_id', 'name']);
        });
    }

    /**
     * Menghapus tabel requirements.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
