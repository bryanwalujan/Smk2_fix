<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up()
       {
           Schema::table('class_sessions', function (Blueprint $table) {
               $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('teacher_id');
           });
       }

       public function down()
       {
           Schema::table('class_sessions', function (Blueprint $table) {
               $table->dropForeign(['created_by']);
               $table->dropColumn('created_by');
           });
       }
   };