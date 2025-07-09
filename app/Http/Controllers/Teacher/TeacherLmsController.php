<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClassSubmissionsExport;

class TeacherLmsController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        $today = Carbon::today()->toDateString();

        // Jadwal hari ini
        $classSessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->with(['classroom', 'subject'])
            ->get();

        // Semua jadwal
        $allClassSessions = ClassSession::where('teacher_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Log untuk memeriksa data class_sessions
        Log::info('Class Sessions Data', [
            'teacher_id' => $teacher->id,
            'today_sessions' => $classSessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'classroom_id' => $session->classroom_id,
                    'classroom_name' => $session->classroom ? $session->classroom->full_name : null,
                    'subject_id' => $session->subject_id,
                    'subject_name' => $session->subject ? $session->subject->name : null,
                    'date' => $session->date,
                    'day_of_week' => $session->day_of_week,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                ];
            })->toArray(),
            'all_sessions' => $allClassSessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'classroom_id' => $session->classroom_id,
                    'classroom_name' => $session->classroom ? $session->classroom->full_name : null,
                    'subject_id' => $session->subject_id,
                    'subject_name' => $session->subject ? $session->subject->name : null,
                    'date' => $session->date,
                    'day_of_week' => $session->day_of_week,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                ];
            })->toArray(),
        ]);

        // Hitung jumlah mata pelajaran unik
        $uniqueSubjectsCount = ClassSession::where('teacher_id', $teacher->id)
            ->distinct('subject_id')
            ->count('subject_id');

        // Ambil daftar kelas dan mata pelajaran dari schedules
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->get();

        $subjectsByClass = [];
        foreach ($schedules as $schedule) {
            if ($schedule->classroom && $schedule->subject) {
                $subjectsByClass[$schedule->classroom->full_name][] = $schedule->subject->name;
            }
        }

        Log::info('Teacher LMS Dashboard', [
            'teacher_id' => $teacher->id,
            'class_sessions_count' => $classSessions->count(),
            'all_class_sessions_count' => $allClassSessions->count(),
            'unique_subjects_count' => $uniqueSubjectsCount,
            'subjects_by_class' => $subjectsByClass,
        ]);

        return view('teacher.lms.index', compact(
            'subjectsByClass',
            'classSessions',
            'allClassSessions',
            'uniqueSubjectsCount'
        ));
    }

    public function showClassSchedules($classroom_id)
    {
        $teacher = Auth::user()->teacher;
        $classroom = Classroom::findOrFail($classroom_id);

        // Pastikan guru memiliki akses ke kelas ini
        $hasAccess = Schedule::where('teacher_id', $teacher->id)
            ->where('classroom_id', $classroom_id)
            ->exists();

        if (!$hasAccess) {
            Log::warning('Unauthorized access to classroom schedules', [
                'teacher_id' => $teacher->id,
                'classroom_id' => $classroom_id,
            ]);
            return redirect()->route('teacher.lms.index')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        // Ambil semua jadwal untuk kelas tertentu
        $classSessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('classroom_id', $classroom_id)
            ->with(['classroom', 'subject'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Ambil semua schedule_id yang terkait dengan kelas
        $scheduleIds = Schedule::where('teacher_id', $teacher->id)
            ->where('classroom_id', $classroom_id)
            ->pluck('id');

        // Ambil semua materi berdasarkan schedule_id
        $materials = Material::whereIn('schedule_id', $scheduleIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil semua tugas berdasarkan schedule_id
        $assignments = Assignment::whereIn('schedule_id', $scheduleIds)
            ->with('submissions')
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('Class Schedules for Classroom', [
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom_id,
            'classroom_name' => $classroom->full_name,
            'class_sessions_count' => $classSessions->count(),
            'materials_count' => $materials->count(),
            'assignments_count' => $assignments->count(),
            'class_sessions' => $classSessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'classroom_id' => $session->classroom_id,
                    'classroom_name' => $session->classroom ? $session->classroom->full_name : null,
                    'subject_id' => $session->subject_id,
                    'subject_name' => $session->subject ? $session->subject->name : null,
                    'date' => $session->date,
                    'day_of_week' => $session->day_of_week,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                ];
            })->toArray(),
        ]);

        return view('teacher.lms.class_schedules', compact('classroom', 'classSessions', 'materials', 'assignments'));
    }

    public function showSession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);

        // Cari schedule berdasarkan atribut dengan penanganan format waktu
        $schedule = Schedule::where('teacher_id', $classSession->teacher_id)
            ->where('classroom_id', $classSession->classroom_id)
            ->where('subject_id', $classSession->subject_id)
            ->where('day', $classSession->day_of_week)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->end_time])
            ->first();

        if (!$schedule) {
            Log::warning('Schedule not found for class session', [
                'class_session_id' => $classSession->id,
                'teacher_id' => $classSession->teacher_id,
                'classroom_id' => $classSession->classroom_id,
                'subject_id' => $classSession->subject_id,
                'day_of_week' => $classSession->day_of_week,
                'start_time' => $classSession->start_time,
                'end_time' => $classSession->end_time,
                'schedules_found' => Schedule::where('teacher_id', $classSession->teacher_id)
                    ->where('classroom_id', $classSession->classroom_id)
                    ->where('subject_id', $classSession->subject_id)
                    ->get()->toArray(),
            ]);
            return redirect()->route('teacher.lms.index')
                ->with('error', 'Jadwal tidak ditemukan di tabel schedules.');
        }

        // Ambil materials dan assignments berdasarkan schedule_id
        $materials = Material::where('schedule_id', $schedule->id)->get();
        $assignments = Assignment::where('schedule_id', $schedule->id)
            ->with('submissions')
            ->get();

        Log::info('Show Session', [
            'class_session_id' => $classSession->id,
            'schedule_id' => $schedule->id,
            'materials_count' => $materials->count(),
            'assignments_count' => $assignments->count(),
        ]);

        return view('teacher.lms.show_session', compact('classSession', 'materials', 'assignments'));
    }

    public function createMaterial(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        if (!ClassSession::where('id', $classSession->id)->exists()) {
            return redirect()->route('teacher.lms.index')->with('error', 'Jadwal tidak ditemukan.');
        }
        return view('teacher.lms.create_material', compact('classSession'));
    }

    public function storeMaterial(Request $request, ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'material.title' => 'required|string|max:100',
            'material.content' => 'nullable|string',
            'material.file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif,mp4,avi,mov,mkv|max:262144',
            'assignments' => 'nullable|array',
            'assignments.*.title' => 'required|string|max:100',
            'assignments.*.description' => 'nullable|string',
            'assignments.*.deadline' => 'required|date|after:now',
            'assignments.*.file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:262144',
        ], [
            'material.title.required' => 'Judul materi wajib diisi.',
            'material.title.max' => 'Judul materi tidak boleh lebih dari 100 karakter.',
            'material.file.mimes' => 'Format file materi tidak valid.',
            'material.file.max' => 'Ukuran file materi maksimum 256 MB.',
            'assignments.*.title.required' => 'Judul tugas wajib diisi.',
            'assignments.*.title.max' => 'Judul tugas tidak boleh lebih dari 100 karakter.',
            'assignments.*.deadline.required' => 'Tenggat waktu tugas wajib diisi.',
            'assignments.*.deadline.after' => 'Tenggat waktu tugas harus setelah waktu saat ini.',
            'assignments.*.file.mimes' => 'Format file tugas tidak valid.',
            'assignments.*.file.max' => 'Ukuran file tugas maksimum 256 MB.',
        ]);

        if (!ClassSession::where('id', $classSession->id)->where('teacher_id', $teacher->id)->exists()) {
            return redirect()->route('teacher.lms.index')->with('error', 'Jadwal tidak valid atau tidak ditemukan.');
        }

        // Cari schedule berdasarkan atribut
        $schedule = Schedule::where('teacher_id', $classSession->teacher_id)
            ->where('classroom_id', $classSession->classroom_id)
            ->where('subject_id', $classSession->subject_id)
            ->where('day', $classSession->day_of_week)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->end_time])
            ->first();

        if (!$schedule) {
            Log::warning('Schedule not found for material creation', [
                'class_session_id' => $classSession->id,
                'teacher_id' => $classSession->teacher_id,
                'classroom_id' => $classSession->classroom_id,
                'subject_id' => $classSession->subject_id,
                'day_of_week' => $classSession->day_of_week,
                'start_time' => $classSession->start_time,
                'end_time' => $classSession->end_time,
            ]);
            return redirect()->route('teacher.lms.index')->with('error', 'Jadwal tidak ditemukan di tabel schedules.');
        }

        $materialData = $request->material;
        $materialData['schedule_id'] = $schedule->id;
        if ($request->hasFile('material.file')) {
            $materialData['file_path'] = $request->file('material.file')->store('materials', 'public');
        }
        $material = Material::create($materialData);

        if ($request->has('assignments')) {
            foreach ($request->assignments as $index => $assignmentData) {
                $assignmentData['schedule_id'] = $schedule->id;
                $assignmentData['material_id'] = $material->id;
                if ($request->hasFile("assignments.$index.file")) {
                    $assignmentData['file_path'] = $request->file("assignments.$index.file")->store('assignments', 'public');
                }
                Assignment::create($assignmentData);
            }
        }

        return redirect()->route('teacher.lms.class_schedules', $classSession->classroom_id)
            ->with('success', 'Materi dan tugas berhasil ditambahkan.');
    }

    public function showMaterial(ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.show_material', compact('classSession', 'material'));
    }

    public function editMaterial(ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.edit_material', compact('classSession', 'material'));
    }

    public function updateMaterial(Request $request, ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif,mp4,avi,mov,mkv|max:262144',
        ]);
        $data = $request->only(['title', 'content']);
        if ($request->hasFile('file')) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }
        $material->update($data);
        return redirect()->route('teacher.lms.class_schedules', $classSession->classroom_id)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyMaterial(ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return redirect()->route('teacher.lms.class_schedules', $classSession->classroom_id)
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function createAssignment(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.create_assignment', compact('classSession'));
    }

    public function storeAssignment(Request $request, ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'deadline' => 'required|date|after:now',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:262144',
        ]);

        // Cari schedule berdasarkan atribut
        $schedule = Schedule::where('teacher_id', $classSession->teacher_id)
            ->where('classroom_id', $classSession->classroom_id)
            ->where('subject_id', $classSession->subject_id)
            ->where('day', $classSession->day_of_week)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->end_time])
            ->first();

        if (!$schedule) {
            Log::warning('Schedule not found for assignment creation', [
                'class_session_id' => $classSession->id,
                'teacher_id' => $classSession->teacher_id,
                'classroom_id' => $classSession->classroom_id,
                'subject_id' => $classSession->subject_id,
                'day_of_week' => $classSession->day_of_week,
                'start_time' => $classSession->start_time,
                'end_time' => $classSession->end_time,
            ]);
            return redirect()->route('teacher.lms.index')->with('error', 'Jadwal tidak ditemukan di tabel schedules.');
        }

        $assignmentData = [
            'schedule_id' => $schedule->id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ];

        if ($request->hasFile('file')) {
            $assignmentData['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create($assignmentData);

        return redirect()->route('teacher.lms.class_schedules', $classSession->classroom_id)
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function showAssignment(ClassSession $classSession, Assignment $assignment)
    {
        // Otorisasi guru untuk classSession
        $this->authorizeTeacher($classSession);

        // Pastikan assignment terkait dengan schedule yang sesuai dengan classSession
        $schedule = Schedule::where('teacher_id', $classSession->teacher_id)
            ->where('classroom_id', $classSession->classroom_id)
            ->where('subject_id', $classSession->subject_id)
            ->where('day', $classSession->day_of_week)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->end_time])
            ->first();

        if (!$schedule || $assignment->schedule_id !== $schedule->id) {
            Log::warning('Invalid assignment or schedule for class session', [
                'class_session_id' => $classSession->id,
                'assignment_id' => $assignment->id,
                'schedule_id' => $assignment->schedule_id,
            ]);
            return redirect()->route('teacher.lms.index')
                ->with('error', 'Tugas atau jadwal tidak valid untuk sesi kelas ini.');
        }

        // Muat data submissions
        $assignment->load(['submissions.student.user', 'submissions.student.classroom']);

        Log::info('Show Assignment', [
            'class_session_id' => $classSession->id,
            'assignment_id' => $assignment->id,
            'schedule_id' => $schedule->id,
            'submissions_count' => $assignment->submissions->count(),
        ]);

        return view('teacher.lms.show_assignment', compact('classSession', 'assignment'));
    }

    public function editAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.edit_assignment', compact('classSession', 'assignment'));
    }

    public function updateAssignment(Request $request, ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:262144',
        ]);

        $assignmentData = [
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ];

        if ($request->hasFile('file')) {
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            $assignmentData['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $assignment->update($assignmentData);
        return redirect()->route('teacher.lms.class_schedules', $classSession->classroom_id)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroyAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }
        $assignment->delete();
        return redirect()->route('teacher.lms.class_schedules', $classSession->classroom_id)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function showSubmissions(Assignment $assignment)
    {
        $schedule = Schedule::find($assignment->schedule_id);

        Log::info('Show Submissions', [
            'assignment_id' => $assignment->id,
            'schedule_id' => $assignment->schedule_id,
        ]);

        if (!$schedule) {
            Log::warning('Schedule not found for assignment', [
                'assignment_id' => $assignment->id,
                'schedule_id' => $assignment->schedule_id,
            ]);
            return redirect()->route('teacher.lms.index')
                ->with('error', 'Jadwal tidak ditemukan untuk tugas ini.');
        }

        $classSession = ClassSession::where('teacher_id', $schedule->teacher_id)
            ->where('classroom_id', $schedule->classroom_id)
            ->where('subject_id', $schedule->subject_id)
            ->where('day_of_week', $schedule->day)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$schedule->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$schedule->end_time])
            ->first();

        if (!$classSession) {
            Log::warning('ClassSession not found for schedule', [
                'assignment_id' => $assignment->id,
                'schedule_id' => $schedule->id,
            ]);
            return redirect()->route('teacher.lms.index')
                ->with('error', 'Sesi kelas tidak ditemukan untuk tugas ini.');
        }

        $this->authorizeTeacher($classSession);

        $assignment->load(['submissions.student.user', 'submissions.student.classroom']);

        return view('teacher.lms.show_submissions', compact('assignment', 'classSession'));
    }

    public function gradeSubmission(Request $request, Assignment $assignment, Submission $submission)
    {
        $schedule = Schedule::find($assignment->schedule_id);

        Log::info('Grade Submission', [
            'assignment_id' => $assignment->id,
            'submission_id' => $submission->id,
            'schedule_id' => $assignment->schedule_id,
        ]);

        if (!$schedule) {
            Log::warning('Schedule not found for assignment', [
                'assignment_id' => $assignment->id,
                'schedule_id' => $assignment->schedule_id,
            ]);
            return redirect()->route('teacher.lms.index')
                ->with('error', 'Jadwal tidak ditemukan untuk tugas ini.');
        }

        $classSession = ClassSession::where('teacher_id', $schedule->teacher_id)
            ->where('classroom_id', $schedule->classroom_id)
            ->where('subject_id', $schedule->subject_id)
            ->where('day_of_week', $schedule->day)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$schedule->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$schedule->end_time])
            ->first();

        if (!$classSession) {
            Log::warning('ClassSession not found for schedule', [
                'assignment_id' => $assignment->id,
                'schedule_id' => $schedule->id,
            ]);
            return redirect()->route('teacher.lms.index')
                ->with('error', 'Sesi kelas tidak ditemukan untuk tugas ini.');
        }

        $this->authorizeTeacher($classSession);

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $submission->update([
            'score' => $request->score,
        ]);

        return redirect()->route('teacher.lms.show_submissions', $assignment)
            ->with('success', 'Nilai tugas berhasil diperbarui.');
    }

    public function showChangePasswordForm()
    {
        return view('teacher.lms.change_password');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Password lama salah.');
                    }
                }
            ],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        return redirect()->route('teacher.lms.index')->with('success', 'Password berhasil diganti.');
    }

    public function showAttendance(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);

        $date = Carbon::parse($classSession->date)->format('Y-m-d');

        $classroom = Classroom::findOrFail($classSession->classroom_id);
        $students = Student::where('classroom_id', $classroom->id)
            ->with([
                'attendances' => function ($query) use ($date) {
                    $query->where('tanggal', $date);
                }
            ])
            ->get();

        Log::info('Show Attendance', [
            'class_session_id' => $classSession->id,
            'classroom_id' => $classroom->id,
            'classroom_name' => $classroom->full_name,
            'date' => $date,
            'students_count' => $students->count(),
            'attendances' => $students->pluck('attendances')->flatten()->toArray(),
        ]);

        return view('teacher.lms.attendance', compact('classSession', 'classroom', 'students'));
    }

    public function updateAttendance(Request $request, ClassSession $classSession, Student $student)
    {
        $this->authorizeTeacher($classSession);

        $request->validate([
            'status' => 'required|in:hadir,tidak_hadir,izin,sakit',
        ]);

        try {
            if ($student->classroom_id !== $classSession->classroom_id) {
                return redirect()->route('teacher.lms.show_attendance', $classSession)
                    ->with('error', 'Siswa tidak terdaftar di kelas ini.');
            }

            $date = Carbon::parse($classSession->date)->format('Y-m-d');

            $attendance = StudentAttendance::where('student_id', $student->id)
                ->where('tanggal', $date)
                ->first();

            if ($attendance) {
                $attendance->update([
                    'status' => $request->status,
                    'metode_absen' => 'manual',
                    'updated_at' => now(),
                ]);
            } else {
                StudentAttendance::create([
                    'student_id' => $student->id,
                    'tanggal' => $date,
                    'waktu_masuk' => now()->format('H:i:s'),
                    'status' => $request->status,
                    'metode_absen' => 'manual',
                ]);
            }

            return redirect()->route('teacher.lms.show_attendance', $classSession)
                ->with('success', 'Status absensi untuk ' . $student->name . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating attendance', [
                'message' => $e->getMessage(),
                'class_session_id' => $classSession->id,
                'student_id' => $student->id,
                'status' => $request->status,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('teacher.lms.show_attendance', $classSession)
                ->with('error', 'Terjadi kesalahan saat memperbarui absensi: ' . $e->getMessage());
        }
    }

    public function exportClassSubmissions(ClassSession $classSession)
    {
        Log::info('Export Class Submissions', [
            'class_session_id' => $classSession->id,
            'classroom_id' => $classSession->classroom_id,
        ]);

        $this->authorizeTeacher($classSession);

        // Cari schedule berdasarkan atribut
        $schedule = Schedule::where('teacher_id', $classSession->teacher_id)
            ->where('classroom_id', $classSession->classroom_id)
            ->where('subject_id', $classSession->subject_id)
            ->where('day', $classSession->day_of_week)
            ->whereRaw('TIME_FORMAT(start_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->start_time])
            ->whereRaw('TIME_FORMAT(end_time, "%H:%i") = TIME_FORMAT(?, "%H:%i")', [$classSession->end_time])
            ->first();

        if (!$schedule) {
            Log::warning('Schedule not found for class session', [
                'class_session_id' => $classSession->id,
                'teacher_id' => $classSession->teacher_id,
                'classroom_id' => $classSession->classroom_id,
                'subject_id' => $classSession->subject_id,
                'day_of_week' => $classSession->day_of_week,
                'start_time' => $classSession->start_time,
                'end_time' => $classSession->end_time,
            ]);
            return redirect()->route('teacher.lms.index')
                ->with('error', 'Jadwal tidak ditemukan untuk sesi kelas ini.');
        }

        $classroomName = $classSession->classroom->full_name ?? 'Unknown';
        $fileName = 'Nilai_Kelas_' . str_replace(' ', '_', $classroomName) . '.xlsx';

        return Excel::download(new ClassSubmissionsExport($classSession), $fileName);
    }

    public function exportClassAttendance($classroom_id)
    {
        $teacher = Auth::user()->teacher;
        $classroom = Classroom::findOrFail($classroom_id);

        // Pastikan guru memiliki akses ke kelas ini
        $hasAccess = Schedule::where('teacher_id', $teacher->id)
            ->where('classroom_id', $classroom_id)
            ->exists();

        if (!$hasAccess) {
            Log::warning('Unauthorized access to classroom attendance', [
                'teacher_id' => $teacher->id,
                'classroom_id' => $classroom_id,
            ]);
            return redirect()->route('teacher.lms.index')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        // Ambil semua sesi kelas untuk kelas ini
        $classSessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('classroom_id', $classroom_id)
            ->pluck('date')
            ->unique();

        // Ambil semua siswa di kelas ini
        $students = Student::where('classroom_id', $classroom_id)
            ->with(['attendances' => function ($query) use ($classSessions) {
                $query->whereIn('tanggal', $classSessions);
            }])
            ->get();

        Log::info('Export Class Attendance', [
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom_id,
            'classroom_name' => $classroom->full_name,
            'students_count' => $students->count(),
            'dates_count' => $classSessions->count(),
        ]);

        $fileName = 'Absensi_Kelas_' . str_replace(' ', '_', $classroom->full_name) . '.xlsx';

        return Excel::download(new \App\Exports\ClassAttendanceExport($classroom, $students, $classSessions), $fileName);
    }

    protected function authorizeTeacher(ClassSession $classSession)
    {
        if ($classSession->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }
    }
    public function clearFlash(Request $request)
{
    session()->forget('success');
    return response()->json(['status' => 'success']);
}
}