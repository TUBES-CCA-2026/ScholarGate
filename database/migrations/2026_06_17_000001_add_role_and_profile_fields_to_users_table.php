<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('password');
            $table->string('nim')->nullable()->unique()->after('role');
            $table->string('program_studi')->nullable()->after('nim');
            $table->string('kelas')->nullable()->after('program_studi');
            $table->decimal('ipk', 3, 2)->nullable()->after('kelas');
            $table->string('phone')->nullable()->after('ipk');
            $table->string('photo_path')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nim', 'program_studi', 'kelas', 'ipk', 'phone', 'photo_path']);
        });
    }
};
