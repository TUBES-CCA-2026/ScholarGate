<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel users sebagai pusat identitas pengguna ScholarGate.
     *
     * Tabel ini sudah memuat atribut autentikasi dan profil mahasiswa agar
     * data personal tidak tersebar pada tabel lain. Kolom role dibatasi pada
     * level aplikasi melalui konstanta model User.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('student')->index();
            $table->string('nim', 50)->nullable()->unique();
            $table->string('program_studi')->nullable();
            $table->string('kelas', 100)->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('photo_path')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel users saat rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
