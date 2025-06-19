<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Helper function untuk cek foreign key ada atau tidak
        $foreignKeyExists = function($table, $constraint) {
            $result = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND CONSTRAINT_NAME = ?
            ", [config('database.connections.mysql.database'), $table, $constraint]);
            
            return !empty($result);
        };

        // Ubah schedules: hapus subject_id, tambah subject_name
        Schema::table('schedules', function (Blueprint $table) use ($foreignKeyExists) {
            // Cek dan drop foreign key jika ada
            if ($foreignKeyExists('schedules', 'schedules_subject_id_foreign')) {
                $table->dropForeign(['subject_id']);
            }
            
            // Drop column jika ada
            if (Schema::hasColumn('schedules', 'subject_id')) {
                $table->dropColumn('subject_id');
            }
            
            // Tambah subject_name jika belum ada
            if (!Schema::hasColumn('schedules', 'subject_name')) {
                $table->string('subject_name')->after('classroom_id');
            }
        });

        // Ubah teacher_classroom_subject: hapus subject_id, tambah subject_name
        Schema::table('teacher_classroom_subject', function (Blueprint $table) use ($foreignKeyExists) {
            // Cek dan drop foreign key jika ada
            if ($foreignKeyExists('teacher_classroom_subject', 'teacher_classroom_subject_subject_id_foreign')) {
                $table->dropForeign(['subject_id']);
            }
            
            // Drop column jika ada
            if (Schema::hasColumn('teacher_classroom_subject', 'subject_id')) {
                $table->dropColumn('subject_id');
            }
            
            // Tambah subject_name jika belum ada
            if (!Schema::hasColumn('teacher_classroom_subject', 'subject_name')) {
                $table->string('subject_name')->after('classroom_id');
            }
        });

        // Hapus tabel subjects jika ada
        Schema::dropIfExists('subjects');
    }

    public function down(): void
    {
        // Buat kembali tabel subjects jika belum ada
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        // Kembalikan teacher_classroom_subject
        if (Schema::hasTable('teacher_classroom_subject')) {
            Schema::table('teacher_classroom_subject', function (Blueprint $table) {
                // Drop subject_name jika ada
                if (Schema::hasColumn('teacher_classroom_subject', 'subject_name')) {
                    $table->dropColumn('subject_name');
                }
                
                // Tambah kembali subject_id jika belum ada
                if (!Schema::hasColumn('teacher_classroom_subject', 'subject_id')) {
                    $table->unsignedBigInteger('subject_id')->after('classroom_id');
                    // Jangan langsung buat foreign key constraint di sini
                    // Biarkan migration lain yang handle foreign key
                }
            });
        }

        // Kembalikan schedules
        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                // Drop subject_name jika ada
                if (Schema::hasColumn('schedules', 'subject_name')) {
                    $table->dropColumn('subject_name');
                }
                
                // Tambah kembali subject_id jika belum ada
                if (!Schema::hasColumn('schedules', 'subject_id')) {
                    $table->unsignedBigInteger('subject_id')->after('classroom_id');
                    // Jangan langsung buat foreign key constraint di sini
                    // Biarkan migration lain yang handle foreign key
                }
            });
        }
    }
};