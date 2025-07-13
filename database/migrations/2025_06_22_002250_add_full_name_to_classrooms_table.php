<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('id');
        });

        // Update existing records to populate full_name
        \DB::table('classrooms')->get()->each(function ($classroom) {
            $fullName = trim("{$classroom->level} {$classroom->major} {$classroom->class_code}");
            \DB::table('classrooms')->where('id', $classroom->id)->update(['full_name' => $fullName]);
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }
};