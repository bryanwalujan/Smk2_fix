<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Teacher::with('subjects')->get()->map(function ($teacher) {
            return [
                'NIP' => $teacher->nip,
                'Nama' => $teacher->name,
                'Email' => $teacher->user->email ?? '-',
                'Mata Pelajaran' => $teacher->subjects->pluck('name')->implode(', ') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['NIP', 'Nama', 'Email', 'Mata Pelajaran'];
    }
}