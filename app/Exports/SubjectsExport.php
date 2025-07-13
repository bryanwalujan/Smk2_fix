<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubjectsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Subject::all()->map(function ($subject) {
            return [
                'Nama Mata Pelajaran' => $subject->name,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Mata Pelajaran'];
    }
}