<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\Schedule;
use App\Models\Submission;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // Kumpulkan aktivitas terkini
        $recentActivities = collect();

        // Aktivitas dari pengumpulan tugas (Submissions)
        $submissions = Submission::where('student_id', $student->id)
            ->with(['assignment.schedule.subject'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($submission) {
                return [
                    'type' => 'task_completed',
                    'description' => 'Anda menyelesaikan tugas <span class="font-semibold">' . ($submission->assignment->title ?? 'Tugas Tanpa Nama') . '</span> untuk mata pelajaran <span class="font-semibold">' . ($submission->assignment->schedule->subject->name ?? 'Unknown') . '</span>',
                    'created_at' => $submission->created_at,
                ];
            });

        // Aktivitas dari materi yang diakses (misalnya, dari log atau riwayat akses)
        // Catatan: Karena controller tidak mencatat akses materi, kita asumsikan logika serupa
        $materials = Material::join('schedules', 'materials.schedule_id', '=', 'schedules.id')
            ->where('schedules.classroom_id', $student->classroom_id)
            ->select('materials.*')
            ->orderBy('materials.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($material) {
                return [
                    'type' => 'material_viewed',
                    'description' => 'Materi baru tersedia: <span class="font-semibold">' . ($material->title ?? 'Materi Tanpa Nama') . '</span> untuk mata pelajaran <span class="font-semibold">' . ($material->schedule->subject->name ?? 'Unknown') . '</span>',
                    'created_at' => $material->created_at,
                ];
            });

        // Gabungkan dan urutkan aktivitas berdasarkan created_at
        $recentActivities = $submissions->merge($materials)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        Log::info('Showing student dashboard', [
            'user_id' => Auth::id(),
            'subjects_count' => $subjects->count(),
            'activities_count' => $recentActivities->count(),
        ]);

        return view('student.lms.index', compact('subjects', 'recentActivities'));
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
        Log::info('Accessing create submission page', [
            'assignment_id' => $assignment->id,
            'schedule_id' => $assignment->schedule_id,
            'user_id' => Auth::id(),
        ]);

        $this->authorizeStudentAssignment($assignment);

        $subject_id = $assignment->schedule ? $assignment->schedule->subject_id : null;
        if (!$subject_id) {
            Log::warning('Assignment missing schedule or subject_id', [
                'assignment_id' => $assignment->id,
                'schedule_id' => $assignment->schedule_id,
            ]);
            return redirect()->route('lms.index')
                ->with('error', 'Tidak dapat mengakses tugas: Mata pelajaran tidak ditemukan.');
        }

        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::user()->student->id)
            ->first();

        return view('student.lms.create_submission', compact('assignment', 'existingSubmission', 'subject_id'));
    }

    public function storeSubmission(Request $request, Assignment $assignment)
    {
        Log::info('Attempting to store submission', [
            'assignment_id' => $assignment->id,
            'user_id' => Auth::id(),
        ]);

        $this->authorizeStudentAssignment($assignment);

        $subject_id = $assignment->schedule ? $assignment->schedule->subject_id : null;
        if (!$subject_id) {
            Log::warning('Assignment missing schedule or subject_id', [
                'assignment_id' => $assignment->id,
                'schedule_id' => $assignment->schedule_id,
            ]);
            return redirect()->route('lms.index')
                ->with('error', 'Tidak dapat mengumpulkan tugas: Mata pelajaran tidak ditemukan.');
        }

        $request->validate([
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $student = Auth::user()->student;
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingSubmission) {
            Log::warning('Submission already exists', [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ]);
            return back()->withErrors(['file' => 'Anda sudah mengumpulkan tugas ini.']);
        }

        if ($assignment->deadline < now()) {
            Log::warning('Submission deadline passed', [
                'assignment_id' => $assignment->id,
                'deadline' => $assignment->deadline,
            ]);
            return back()->withErrors(['deadline' => 'Tenggat waktu pengumpulan telah lewat.']);
        }

        $data = [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('submissions', 'public');
            Log::info('File uploaded', [
                'assignment_id' => $assignment->id,
                'file_path' => $data['file_path'],
            ]);
        }

        Submission::create($data);

        Log::info('Submission created successfully', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ]);

        return redirect()->route('lms.show_assignment', [$subject_id, $assignment->id])
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    protected function authorizeStudent(ClassSession $classSession)
    {
        if ($classSession->classroom_id !== Auth::user()->student->classroom_id) {
            Log::warning('Authorization failed for class session', [
                'class_session_id' => $classSession->id,
                'student_classroom_id' => Auth::user()->student->classroom_id,
            ]);
            abort(403, 'Unauthorized');
        }
    }

    protected function authorizeStudentAssignment(Assignment $assignment)
    {
        $student = Auth::user()->student;

        Log::info('Authorizing student assignment', [
            'assignment_id' => $assignment->id,
            'schedule_id' => $assignment->schedule_id,
            'student_classroom_id' => $student->classroom_id,
        ]);

        $schedule = Schedule::where('id', $assignment->schedule_id)
            ->where('classroom_id', $student->classroom_id)
            ->first();

        if (!$schedule) {
            $classSession = ClassSession::where('classroom_id', $student->classroom_id)
                ->where('subject_id', $assignment->schedule->subject_id)
                ->first();

            if (!$classSession) {
                Log::warning('Authorization failed: No matching schedule or class session', [
                    'assignment_id' => $assignment->id,
                    'student_classroom_id' => $student->classroom_id,
                ]);
                abort(403, 'Unauthorized: You do not have access to this assignment.');
            }
        }
    }
}