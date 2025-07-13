<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation
{
    public $errors = [];

    public function model(array $row)
    {
        try {
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
                'role' => 'teacher',
            ]);

            $barcode = rand(100000, 999999);
            $teacher = Teacher::create([
                'nip' => $row['nip'],
                'name' => $row['name'],
                'barcode' => $barcode,
                'user_id' => $user->id,
            ]);

            if (!empty($row['subject_ids'])) {
                $subjectIds = array_map('trim', explode(',', $row['subject_ids']));
                $teacher->subjects()->sync($subjectIds);
            }

            if (!empty($row['classroom_id'])) {
                $teacher->classrooms()->sync([$row['classroom_id']]);
            }

            return $teacher;
        } catch (\Exception $e) {
            $this->errors[] = "Error in row {$row['name']}: {$e->getMessage()}";
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nip' => 'required|unique:teachers',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'subject_ids' => 'nullable|string',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}