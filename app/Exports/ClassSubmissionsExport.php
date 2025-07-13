<?php

namespace App\Exports;

use App\Models\ClassSession;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\Schedule;
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
    protected $classroom;
    protected $subject;

    public function __construct(ClassSession $classSession)
    {
        $this->classSession = $classSession;

        // Fetch classroom and subject details
        $this->classroom = $classSession->classroom;
        $this->subject = $classSession->subject;

        // Fetch the schedule for the class session
        $schedule = Schedule::where('classroom_id', $classSession->classroom_id)
            ->where('subject_id', $classSession->subject_id)
            ->where('teacher_id', $classSession->teacher_id)
            ->where('day', $classSession->day_of_week)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->end_time])
            ->first();

        // Fetch all assignments for the schedule
        $this->assignments = $schedule
            ? Assignment::where('schedule_id', $schedule->id)
                ->orderBy('created_at')
                ->get()
            : collect([]);

        Log::info('ClassSubmissionsExport initialized', [
            'class_session_id' => $classSession->id,
            'classroom_id' => $classSession->classroom_id,
            'subject_id' => $classSession->subject_id,
            'schedule_id' => $schedule ? $schedule->id : null,
            'assignments_count' => $this->assignments->count(),
            'assignments' => $this->assignments->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'schedule_id' => $assignment->schedule_id,
                ];
            })->toArray(),
        ]);
    }

    public function collection()
    {
        // Fetch all students in the classroom with their submissions
        $students = Student::where('classroom_id', $this->classSession->classroom_id)
            ->with(['user', 'submissions' => function ($query) {
                $query->whereIn('assignment_id', $this->assignments->pluck('id'))
                    ->with('assignment');
            }])
            ->orderBy('nis')
            ->get();

        Log::info('Fetched students for export', [
            'classroom_id' => $this->classSession->classroom_id,
            'students_count' => $students->count(),
            'student_ids' => $students->pluck('id')->toArray(),
        ]);

        return $students;
    }

    public function headings(): array
    {
        // Base headings
        $headings = ['NIS', 'Nama Siswa'];

        // Add assignment titles as columns
        foreach ($this->assignments as $assignment) {
            $headings[] = $assignment->title;
        }

        // Log headings for debugging
        Log::info('Export headings', [
            'headings' => $headings,
        ]);

        return $headings;
    }

    public function map($student): array
    {
        $row = [
            $student->nis ?? '-',
            $student->user ? $student->user->name : '-',
        ];

        // Map scores for each assignment
        foreach ($this->assignments as $assignment) {
            $submission = $student->submissions->firstWhere('assignment_id', $assignment->id);
            $row[] = $submission && !is_null($submission->score) ? $submission->score : '-';
        }

        // Log mapped row for debugging
        Log::info('Mapped student row', [
            'student_id' => $student->id,
            'nis' => $student->nis,
            'name' => $student->user ? $student->user->name : '-',
            'scores' => array_slice($row, 2),
        ]);

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        // Add classroom and subject info above the table
        $sheet->setCellValue('A1', 'Kelas: ' . ($this->classroom ? $this->classroom->full_name : 'Unknown'));
        $sheet->setCellValue('A2', 'Mata Pelajaran: ' . ($this->subject ? $this->subject->name : 'Unknown'));
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');

        // Style the header row
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'left']],
            2 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'left']],
            3 => ['font' => ['bold' => true]], // Header row for data
        ];
    }
}