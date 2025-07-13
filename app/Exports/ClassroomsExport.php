<?php

namespace App\Exports;

use App\Models\Classroom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassroomsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Classroom::all()->map(function ($classroom) {
            return [
                'Tingkat' => $classroom->level,
                'Jurusan' => $classroom->major,
                'Kode Kelas' => $classroom->class_code,
                'Nama Lengkap' => $classroom->full_name,
            ];
        });
    }

    public function headings(): array
    {
        return ['Tingkat', 'Jurusan', 'Kode Kelas', 'Nama Lengkap'];
    }
}