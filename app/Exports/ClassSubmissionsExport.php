<?php

namespace App\Exports;

use App\Models\ClassSession;
use App\Models\Student;
use App\Models\Assignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;

class ClassSubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $classSession;
    protected $assignments;

    public function __construct(ClassSession $classSession)
    {
        $this->classSession = $classSession;
        // Ambil semua tugas terkait kelas melalui schedule
        $schedule = $classSession->schedule;
        $this->assignments = $schedule ? Assignment::where('schedule_id', $schedule->id)->get() : collect([]);
    }

    public function collection()
    {
        // Ambil semua siswa di kelas
        return Student::where('classroom_id', $this->classSession->classroom_id)
            ->with(['submissions' => function ($query) {
                $query->whereIn('assignment_id', $this->assignments->pluck('id'));
            }])
            ->get();
    }

    public function headings(): array
    {
        $headings = ['NIS', 'Nama Siswa'];
        foreach ($this->assignments as $assignment) {
            $headings[] = $assignment->title;
        }
        return $headings;
    }

    public function map($student): array
    {
        $row = [
            $student->nis,
            $student->name,
        ];

        // Tambahkan nilai untuk setiap tugas
        foreach ($this->assignments as $assignment) {
            $submission = $student->submissions->firstWhere('assignment_id', $assignment->id);
            $row[] = $submission ? ($submission->score ?? '-') : '-';
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}