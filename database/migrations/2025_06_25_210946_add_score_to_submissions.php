<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up()
       {
           Schema::table('submissions', function (Blueprint $table) {
               $table->decimal('score', 5, 2)->nullable()->after('file_path');
           });
       }

       public function down()
       {
           Schema::table('submissions', function (Blueprint $table) {
               $table->dropColumn('score');
           });
       }
   };