<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToClassSessions extends Migration
{
    public function up()
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->date('date')->nullable()->after('day_of_week');
        });
    }

    public function down()
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
}