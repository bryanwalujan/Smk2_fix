<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\Schedule;
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
            ->with(['teacher.user', 'subject'])
            ->get();

        $pastSessions = $classSessions->filter(function ($session) use ($today) {
            return Carbon::parse($session->date)->lt($today);
        });

        $currentWeekSessions = $classSessions->filter(function ($session) use ($startOfWeek, $endOfWeek) {
            return Carbon::parse($session->date)->between($startOfWeek, $endOfWeek);
        });

        $upcomingSessions = $classSessions->filter(function ($session) use ($endOfWeek) {
            return Carbon::parse($session->date)->gt($endOfWeek);
        });

        // Fetch materials and assignments via schedules
        $materials = Material::join('schedules', 'materials.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->where('schedules.subject_id', $subject->id)
            ->select('materials.*')
            ->get();

        $assignments = Assignment::join('schedules', 'assignments.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->where('schedules.subject_id', $subject->id)
            ->select('assignments.*')
            ->with(['submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->get();

        // Get teacher from the first available session, if any
        $teacherName = $classSessions->first() ? ($classSessions->first()->teacher->user->name ?? 'Belum ada guru') : 'Belum ada guru';

        Log::info('Showing subject sessions', [
            'subject_id' => $subject->id,
            'classroom_id' => $student->classroom_id,
            'materials_count' => $materials->count(),
            'assignments_count' => $assignments->count(),
            'sessions_count' => $classSessions->count(),
        ]);

        return view('student.lms.subject_sessions', compact('subject', 'pastSessions', 'currentWeekSessions', 'upcomingSessions', 'materials', 'assignments', 'teacherName'));
    }

    public function showSession(ClassSession $classSession)
    {
        $this->authorizeStudent($classSession);

        $schedule = Schedule::where('classroom_id', $classSession->classroom_id)
            ->where('subject_id', $classSession->subject_id)
            ->first();

        if (!$schedule) {
            Log::warning('No schedule found for class session', [
                'class_session_id' => $classSession->id,
            ]);
            $materials = collect();
            $assignments = collect();
        } else {
            $materials = Material::where('schedule_id', $schedule->id)->get();
            $assignments = Assignment::where('schedule_id', $schedule->id)
                ->with(['submissions' => function ($query) {
                    $query->where('student_id', Auth::user()->student->id);
                }])
                ->get();
        }

        Log::info('Showing session details', [
            'class_session_id' => $classSession->id,
            'materials_count' => $materials->count(),
            'assignments_count' => $assignments->count(),
        ]);

        return view('student.lms.show_session', compact('classSession', 'materials', 'assignments'));
    }

    public function showMaterial(Subject $subject, Material $material)
    {
        $student = Auth::user()->student;
        $schedule = Schedule::where('id', $material->schedule_id)
            ->where('classroom_id', $student->classroom_id)
            ->where('subject_id', $subject->id)
            ->first();

        if (!$schedule) {
            abort(403, 'Unauthorized');
        }

        Log::info('Showing material details', [
            'material_id' => $material->id,
            'subject_id' => $subject->id,
            'classroom_id' => $student->classroom_id,
        ]);

        return view('student.lms.show_material', compact('subject', 'material'));
    }

    public function subjectMaterials(Subject $subject)
    {
        $student = Auth::user()->student;
        $materials = Material::join('schedules', 'materials.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->where('schedules.subject_id', $subject->id)
            ->select('materials.*')
            ->get();

        Log::info('Showing all materials for subject', [
            'subject_id' => $subject->id,
            'classroom_id' => $student->classroom_id,
            'materials_count' => $materials->count(),
        ]);

        return view('student.lms.subject_materials', compact('subject', 'materials'));
    }

    public function showAssignment(Subject $subject, Assignment $assignment)
    {
        $student = Auth::user()->student;
        $schedule = Schedule::where('id', $assignment->schedule_id)
            ->where('classroom_id', $student->classroom_id)
            ->where('subject_id', $subject->id)
            ->first();

        if (!$schedule) {
            abort(403, 'Unauthorized');
        }

        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        Log::info('Showing assignment details', [
            'assignment_id' => $assignment->id,
            'subject_id' => $subject->id,
            'classroom_id' => $student->classroom_id,
            'has_submission' => $submission ? true : false,
        ]);

        return view('student.lms.show_assignment', compact('subject', 'assignment', 'submission'));
    }

    public function subjectAssignments(Subject $subject)
    {
        $student = Auth::user()->student;
        $assignments = Assignment::join('schedules', 'assignments.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->where('schedules.subject_id', $subject->id)
            ->select('assignments.*')
            ->with(['submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->get();

        Log::info('Showing all assignments for subject', [
            'subject_id' => $subject->id,
            'classroom_id' => $student->classroom_id,
            'assignments_count' => $assignments->count(),
        ]);

        return view('student.lms.subject_assignments', compact('subject', 'assignments'));
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

        $classSession = ClassSession::where('classroom_id', $student->classroom_id)
            ->where('subject_id', $assignment->subject_id)
            ->first();

        return redirect()->route('lms.show_session', $classSession ? $classSession->id : 1)
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
        $student = Auth::user()->student;
        $schedule = Schedule::where('id', $assignment->schedule_id)
            ->where('classroom_id', $student->classroom_id)
            ->where('subject_id', $assignment->subject_id)
            ->first();

        if (!$schedule) {
            abort(403, 'Unauthorized');
        }
    }
}