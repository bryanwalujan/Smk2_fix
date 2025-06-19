<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     public function run(): void
//     {
//         $this->call([
//             AdminSeeder::class,
//             ClassroomSeeder::class,
//         ]);
//     }
// }



namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create qrcodes directory if not exists
        if (!File::exists(public_path('qrcodes'))) {
            File::makeDirectory(public_path('qrcodes'), 0755, true);
        }

        // Create Admin User
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Teacher
        $teacherUser = User::create([
            'name' => 'Diana Rahayu, S.Pd',
            'email' => 'diana.rahayu@school.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $barcodeIdTeacher = rand(100000, 999999);
        Teacher::create([
            'user_id' => $teacherUser->id,
            'name' => 'Diana Rahayu, S.Pd',
            'nip' => '198511212015022001',
            'barcode' => $barcodeIdTeacher,
        ]);

        // Improved QR Code for teacher
        QrCode::format('svg')
              ->size(400)
              ->margin(15)
              ->errorCorrection('H')
              ->color(40, 40, 40)
              ->backgroundColor(245, 245, 245)
              ->generate((string)$barcodeIdTeacher, public_path('qrcodes/teacher_'.$barcodeIdTeacher.'.svg'));

        // Create Classroom
        $classroom = Classroom::create([
            'level' => '12',
            'major' => 'Multimedia',
            'class_code' => 'A',
        ]);

        // Create Student
        $studentUser = User::create([
            'name' => 'Ardi Wibowo',
            'email' => 'ardi.wibowo@school.com',
            'password' => Hash::make('studentPass789'),
            'role' => 'student',
        ]);

        $barcodeIdStudent = rand(100000, 999999);
        while ($barcodeIdStudent == $barcodeIdTeacher) {
            $barcodeIdStudent = rand(100000, 999999);
        }

        Student::create([
            'user_id' => $studentUser->id,
            'name' => 'Ardi Wibowo',
            'nis' => '202411001',
            'classroom_id' => $classroom->id,
            'barcode' => $barcodeIdStudent,
        ]);

        // Improved QR Code for student
        QrCode::format('svg')
              ->size(400)
              ->margin(15)
              ->errorCorrection('H')
              ->color(0, 75, 150)
              ->backgroundColor(255, 255, 255)
              ->generate((string)$barcodeIdStudent, public_path('qrcodes/student_'.$barcodeIdStudent.'.svg'));

        $this->command->info('Seeder executed successfully!');
        $this->command->info('Teacher QR: public/qrcodes/teacher_'.$barcodeIdTeacher.'.svg');
        $this->command->info('Student QR: public/qrcodes/student_'.$barcodeIdStudent.'.svg');
    }
}