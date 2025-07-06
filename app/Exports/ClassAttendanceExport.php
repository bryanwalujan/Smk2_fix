<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassAttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $classroom;
    protected $students;
    protected $dates;

    public function __construct(Classroom $classroom, $students, $dates)
    {
        $this->classroom = $classroom;
        $this->students = $students;
        $this->dates = $dates->sort();
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        $headings = ['Nama Siswa'];
        foreach ($this->dates as $date) {
            $headings[] = \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        }
        return $headings;
    }

    public function map($student): array
    {
        $row = [$student->name];
        foreach ($this->dates as $date) {
            $attendance = $student->attendances->where('tanggal', $date)->first();
            $row[] = $attendance ? $attendance->status : 'Tidak Ada';
        }
        return $row;
    }
}