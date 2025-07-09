<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\Schedule;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $today = Carbon::today();

        // Jumlah Mata Pelajaran
        $subjects = ClassSession::where('classroom_id', $student->classroom_id)
            ->with('subject')
            ->select('subject_id')
            ->distinct()
            ->get()
            ->pluck('subject')
            ->filter();

        // Tugas Aktif (belum dikumpulkan dan belum melewati tenggat waktu)
        $activeAssignments = Assignment::join('schedules', 'assignments.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->where('assignments.deadline', '>=', $today)
            ->whereDoesntHave('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->count();

        // Persentase Kehadiran (asumsi: semua sesi kelas dihitung, tanpa data kehadiran spesifik)
        $totalSessions = ClassSession::where('classroom_id', $student->classroom_id)
            ->where('date', '<=', $today)
            ->count();
        $attendancePercentage = $totalSessions > 0 ? round(($totalSessions / $totalSessions) * 100) : 0;

        // Pemberitahuan (tugas atau materi baru dalam 7 hari terakhir)
        $notifications = Material::join('schedules', 'materials.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->where('materials.created_at', '>=', $today->subDays(7))
            ->count() + Assignment::join('schedules', 'assignments.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->where('assignments.created_at', '>=', $today->subDays(7))
            ->count();

        // Statistik untuk Quick Stats
        $quickStats = [
            'subjects_count' => $subjects->count(),
            'active_assignments' => $activeAssignments,
            'attendance_percentage' => $attendancePercentage,
            'notifications' => $notifications,
        ];

        Log::info('Showing student dashboard', [
            'user_id' => Auth::id(),
            'subjects_count' => $subjects->count(),
            'active_assignments' => $activeAssignments,
            'attendance_percentage' => $attendancePercentage,
            'notifications' => $notifications,
        ]);

        return view('student.dashboard', compact('quickStats'));
    }
}