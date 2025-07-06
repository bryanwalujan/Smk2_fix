<?php

namespace App\Imports;

use App\Models\Classroom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class ClassroomsImport implements ToModel, WithHeadingRow, WithValidation
{
    public $errors = [];

    public function model(array $row)
    {
        try {
            // Generate full_name if not provided
            $fullName = isset($row['full_name']) && !empty(trim($row['full_name']))
                ? trim($row['full_name'])
                : trim("{$row['level']} {$row['major']} {$row['class_code']}");

            return Classroom::create([
                'level' => trim($row['level']),
                'major' => trim($row['major']),
                'class_code' => trim($row['class_code']),
                'full_name' => $fullName,
            ]);
        } catch (\Exception $e) {
            $this->errors[] = "Error in row {$row['level']} {$row['major']} {$row['class_code']}: {$e->getMessage()}";
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'level' => 'required|string|max:10',
            'major' => 'required|string|max:50',
            'class_code' => 'required|string|max:10',
            'full_name' => 'nullable|string|max:100',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
