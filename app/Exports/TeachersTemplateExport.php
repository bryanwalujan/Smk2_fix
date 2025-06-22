<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'nip',
            'name',
            'email',
            'password',
            'subject_ids', // Comma-separated subject IDs
            'classroom_id',
        ];
    }
}