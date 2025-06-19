<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        Classroom::create([
            'level' => '10',
            'major' => 'RPL',
            'class_code' => 'C',
        ]);
        Classroom::create([
            'level' => '10',
            'major' => 'TKJ',
            'class_code' => 'A',
        ]);
        Classroom::create([
            'level' => '11',
            'major' => 'RPL',
            'class_code' => 'B',
        ]);
    }
}