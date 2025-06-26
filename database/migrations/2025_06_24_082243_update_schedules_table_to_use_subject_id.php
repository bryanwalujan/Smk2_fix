<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSchedulesTableToUseSubjectId extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('subject_name');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade')->after('teacher_id');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
            $table->string('subject_name', 100)->after('teacher_id');
        });
    }
}