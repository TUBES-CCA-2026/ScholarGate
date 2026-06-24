<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'mahasiswa')
            ->update(['role' => 'student']);
    }

    public function down(): void
    {
        //
    }
};