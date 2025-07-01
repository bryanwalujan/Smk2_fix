<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StudentLmsController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $subjects = ClassSession::where('classroom_id', $student->classroom_id)
            ->with('subject')
            ->select('subject_id')
            ->distinct()
            ->get()
            ->pluck('subject')
            ->filter();

        return view('student.lms.index', compact('subjects'));
    }

    public function subjectSessions(Subject $subject)
    {
        $student = Auth::user()->student;
        $today = Carbon::today();
        $startOfWeek = $today->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $today->endOfWeek(Carbon::SUNDAY);

        $classSessions = ClassSession::where('classroom_id', $student->classroom_id)
            ->where('subject_id', $subject->id)
            ->with(['teacher', 'subject'])
            ->get();

        // Kelompokkan sesi berdasarkan waktu
        $pastSessions = $classSessions->filter(function ($session) use ($today) {
            return Carbon::parse($session->date)->lt($today);
        });

        $currentWeekSessions = $classSessions->filter(function ($session) use ($startOfWeek, $endOfWeek) {
            return Carbon::parse($session->date)->between($startOfWeek, $endOfWeek);
        });

        $upcomingSessions = $classSessions->filter(function ($session) use ($endOfWeek) {
            return Carbon::parse($session->date)->gt($endOfWeek);
        });

        return view('student.lms.subject_sessions', compact('subject', 'pastSessions', 'currentWeekSessions', 'upcomingSessions'));
    }

    public function showSession(ClassSession $classSession)
    {
        $this->authorizeStudent($classSession);
        return view('student.lms.show_session', compact('classSession'));
    }

    public function createSubmission(Assignment $assignment)
    {
        $this->authorizeStudentAssignment($assignment);
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::user()->student->id)
            ->first();
        return view('student.lms.create_submission', compact('assignment', 'existingSubmission'));
    }

    public function storeSubmission(Request $request, Assignment $assignment)
    {
        $this->authorizeStudentAssignment($assignment);
        $request->validate([
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $student = Auth::user()->student;
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingSubmission) {
            return back()->withErrors(['file' => 'Anda sudah mengumpulkan tugas ini.']);
        }

        if ($assignment->deadline < now()) {
            return back()->withErrors(['deadline' => 'Tenggat waktu pengumpulan telah lewat.']);
        }

        $data = [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('submissions', 'public');
        }

        Submission::create($data);

        return redirect()->route('lms.show_session', $assignment->class_session_id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    protected function authorizeStudent(ClassSession $classSession)
    {
        if ($classSession->classroom_id !== Auth::user()->student->classroom_id) {
            abort(403, 'Unauthorized');
        }
    }

    protected function authorizeStudentAssignment(Assignment $assignment)
    {
        $classSession = ClassSession::find($assignment->class_session_id);
        if (!$classSession || $classSession->classroom_id !== Auth::user()->student->classroom_id) {
            abort(403, 'Unauthorized');
        }
    }
}