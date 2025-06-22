<?php

namespace App\Exports;

use App\Models\StudentAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return StudentAttendance::with('student')->get()->map(function ($attendance) {
            return [
                'NIS' => $attendance->student->nis ?? '-',
                'Nama' => $attendance->student->name ?? '-',
                'Tanggal' => $attendance->tanggal,
                'Waktu Masuk' => $attendance->waktu_masuk ?? '-',
                'Waktu Pulang' => $attendance->waktu_pulang ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['NIS', 'Nama', 'Tanggal', 'Waktu Masuk', 'Waktu Pulang'];
    }
}