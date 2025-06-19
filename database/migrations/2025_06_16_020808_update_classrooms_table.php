<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            // Hapus kolom name
            $table->dropColumn('name');
            // Tambahkan kolom baru
            $table->string('level'); // Tingkat, misalnya: 10, 11, 12
            $table->string('major'); // Jurusan, misalnya: RPL, TKJ
            $table->string('class_code'); // Kode kelas, misalnya: A, B, C
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            // Kembalikan kolom name
            $table->string('name');
            // Hapus kolom baru
            $table->dropColumn(['level', 'major', 'class_code']);
        });
    }
};