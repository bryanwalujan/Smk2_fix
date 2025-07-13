<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  return new class extends Migration
  {
      public function up()
      {
          Schema::table('student_attendances', function (Blueprint $table) {
              $table->unique(['student_id', 'tanggal'], 'student_attendances_student_id_tanggal_unique');
          });
      }

      public function down()
      {
          Schema::table('student_attendances', function (Blueprint $table) {
              $table->dropUnique('student_attendances_student_id_tanggal_unique');
          });
      }
  };