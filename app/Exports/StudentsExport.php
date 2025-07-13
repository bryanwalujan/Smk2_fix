<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Student::with('classroom')->get()->map(function ($student) {
            return [
                'NIS' => $student->nis,
                'Nama' => $student->name,
                'Email' => $student->user->email ?? '-',
                'Kelas' => $student->classroom->full_name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['NIS', 'Nama', 'Email', 'Kelas'];
    }
}