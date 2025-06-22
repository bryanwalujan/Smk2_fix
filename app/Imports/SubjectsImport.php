<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectsImport implements ToModel, WithHeadingRow, WithValidation
{
    public $errors = [];

    public function model(array $row)
    {
        try {
            return Subject::create([
                'name' => $row['name'],
            ]);
        } catch (\Exception $e) {
            $this->errors[] = "Error in row {$row['name']}: {$e->getMessage()}";
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:subjects',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}