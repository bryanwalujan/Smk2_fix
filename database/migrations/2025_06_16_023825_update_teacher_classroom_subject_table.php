<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_classroom_subject', function (Blueprint $table) {
            // Cek dan drop foreign key jika ada
            $foreignKeyExists = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND CONSTRAINT_NAME = ?
            ", [config('database.connections.mysql.database'), 'teacher_classroom_subject', 'teacher_classroom_subject_subject_id_foreign']);
            
            if (!empty($foreignKeyExists)) {
                $table->dropForeign(['subject_id']);
            }
            
            // Drop column subject_id jika ada
            if (Schema::hasColumn('teacher_classroom_subject', 'subject_id')) {
                $table->dropColumn('subject_id');
            }
            
            // Tambah subject_name jika belum ada
            if (!Schema::hasColumn('teacher_classroom_subject', 'subject_name')) {
                $table->string('subject_name')->after('classroom_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teacher_classroom_subject', function (Blueprint $table) {
            // Drop subject_name jika ada
            if (Schema::hasColumn('teacher_classroom_subject', 'subject_name')) {
                $table->dropColumn('subject_name');
            }
            
            // Tambah kembali subject_id jika belum ada
            if (!Schema::hasColumn('teacher_classroom_subject', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->after('classroom_id');
                // Jangan langsung buat foreign key constraint
                // Biarkan migration lain yang handle ini
            }
        });
    }
};