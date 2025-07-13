<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysToSchedulesInMaterialsAndAssignments extends Migration
{
    public function up()
    {
        // Ubah tabel materials
        Schema::table('materials', function (Blueprint $table) {
            // Hapus kunci asing lama jika ada
            if (Schema::hasColumn('materials', 'class_session_id')) {
                $table->dropForeign(['class_session_id']);
                $table->renameColumn('class_session_id', 'schedule_id');
            }
            // Tambahkan kunci asing baru ke schedules.id
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });

        // Ubah tabel assignments
        Schema::table('assignments', function (Blueprint $table) {
            // Hapus kunci asing lama jika ada
            if (Schema::hasColumn('assignments', 'class_session_id')) {
                $table->dropForeign(['class_session_id']);
                $table->renameColumn('class_session_id', 'schedule_id');
            }
            // Tambahkan kunci asing baru ke schedules.id
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Kembalikan perubahan untuk materials
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->renameColumn('schedule_id', 'class_session_id');
            $table->foreign('class_session_id')->references('id')->on('class_sessions')->onDelete('cascade');
        });

        // Kembalikan perubahan untuk assignments
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'schedule_id')) {
                $table->dropForeign(['schedule_id']);
                $table->renameColumn('schedule_id', 'class_session_id');
                $table->foreign('class_session_id')->references('id')->on('class_sessions')->onDelete('cascade');
            }
        });
    }
}