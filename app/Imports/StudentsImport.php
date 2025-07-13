<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public $errors = [];

    public function model(array $row)
    {
        try {
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
                'role' => 'student',
            ]);

            $student = Student::create([
                'nis' => $row['nis'],
                'name' => $row['name'],
                'user_id' => $user->id,
                'classroom_id' => $row['classroom_id'],
            ]);

            return $student;
        } catch (\Exception $e) {
            $this->errors[] = "Error in row {$row['name']}: {$e->getMessage()}";
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|unique:students',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'classroom_id' => 'required|exists:classrooms,id',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}