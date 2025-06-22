<?php

namespace App\Imports;

use App\Models\Classroom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClassroomsImport implements ToModel, WithHeadingRow, WithValidation
{
    public $errors = [];

    public function model(array $row)
    {
        try {
            return Classroom::create([
                'name' => $row['name'],
                'full_name' => $row['full_name'],
            ]);
        } catch (\Exception $e) {
            $this->errors[] = "Error in row {$row['name']}: {$e->getMessage()}";
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'full_name' => 'required|string|max:100',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}