<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\TeacherClassroomSubject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class TeacherLmsController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        // Get teacher's assigned subjects and classrooms
        $subjects = TeacherClassroomSubject::where('teacher_id', $teacher->id)
            ->pluck('subject_name', 'classroom_id')
            ->toArray();
        
        // Get today's classes
        $today = Carbon::today()->translatedFormat('l'); // e.g., "Senin"
        $classSessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->with('classroom')
            ->get();
        
        // Get all classes (for the new section)
        $allClassSessions = ClassSession::where('teacher_id', $teacher->id)
            ->with('classroom')
            ->orderByRaw("FIELD(day_of_week, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('start_time')
            ->get()
            ->map(function ($session) use ($today) {
                $session->is_today = $session->day_of_week === $today;
                return $session;
            });
        
        // Count unique subjects
        $uniqueSubjectsCount = TeacherClassroomSubject::where('teacher_id', $teacher->id)
            ->distinct('subject_name')
            ->count('subject_name');
        
        return view('teacher.lms.index', compact(
            'subjects',
            'classSessions',
            'allClassSessions',
            'uniqueSubjectsCount'
        ));
    }

    public function createSession()
    {
        $teacher = Auth::user()->teacher;
        // Ambil semua kelas
        $classrooms = Classroom::all()->pluck('full_name', 'id')->toArray();
        // Ambil semua mata pelajaran berdasarkan teacher_id saja
        $subjects = TeacherClassroomSubject::where('teacher_id', $teacher->id)
            ->pluck('subject_name')
            ->unique()
            ->values()
            ->toArray();
        return view('teacher.lms.create_session', compact('classrooms', 'subjects'));
    }

    public function storeSession(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_name' => 'required|string|max:255',
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Validasi bahwa subject_name sesuai dengan teacher_classroom_subject untuk teacher_id
        $validSubject = TeacherClassroomSubject::where('teacher_id', $teacher->id)
            ->where('subject_name', $request->subject_name)
            ->exists();

        if (!$validSubject) {
            return back()->withErrors(['subject_name' => 'Mata pelajaran tidak valid untuk guru ini.']);
        }

        ClassSession::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $request->classroom_id,
            'subject_name' => $request->subject_name,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('teacher.lms.index')->with('success', 'Sesi kelas berhasil dibuat.');
    }

    public function editSession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $teacher = Auth::user()->teacher;
        // Ambil semua kelas
        $classrooms = Classroom::all()->pluck('full_name', 'id')->toArray();
        // Ambil semua mata pelajaran berdasarkan teacher_id saja
        $subjects = TeacherClassroomSubject::where('teacher_id', $teacher->id)
            ->pluck('subject_name')
            ->unique()
            ->values()
            ->toArray();
        return view('teacher.lms.edit_session', compact('classSession', 'classrooms', 'subjects'));
    }

    public function updateSession(Request $request, ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $teacher = Auth::user()->teacher;
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_name' => 'required|string|max:255',
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Validasi bahwa subject_name sesuai dengan teacher_classroom_subject untuk teacher_id
        $validSubject = TeacherClassroomSubject::where('teacher_id', $teacher->id)
            ->where('subject_name', $request->subject_name)
            ->exists();

        if (!$validSubject) {
            return back()->withErrors(['subject_name' => 'Mata pelajaran tidak valid untuk guru ini.']);
        }

        $classSession->update($request->only([
            'classroom_id', 'subject_name', 'day_of_week', 'start_time', 'end_time'
        ]));

        return redirect()->route('teacher.lms.index')->with('success', 'Sesi kelas berhasil diperbarui.');
    }

    public function showSession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $classSession->load('materials', 'assignments.submissions');
        return view('teacher.lms.show_session', compact('classSession'));
    }

    public function destroySession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $classSession->delete();
        return redirect()->route('teacher.lms.index')->with('success', 'Sesi kelas berhasil dihapus.');
    }

    public function createMaterial(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.create_material', compact('classSession'));
    }

    public function storeMaterial(Request $request, ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif,mp4,avi,mov,mkv|max:262144',
        ]);

        $data = $request->only(['title', 'content']);
        $data['class_session_id'] = $classSession->id;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        Material::create($data);

        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Materi berhasil ditambahkan.');
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
            // Delete old file if exists
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        $material->update($data);

        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyMaterial(ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Materi berhasil dihapus.');
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
        ]);

        Assignment::create([
            'class_session_id' => $classSession->id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function showAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        $assignment->load('submissions');
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
        ]);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroyAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        $assignment->delete();
        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Tugas berhasil dihapus.');
    }

    public function showSubmissions(Assignment $assignment)
    {
        $this->authorizeTeacher($assignment->classSession);
        $assignment->load(['submissions.student.user', 'submissions.student.classroom', 'classSession']);
        return view('teacher.lms.show_submissions', compact('assignment'));
    }

    public function showChangePasswordForm()
    {
        return view('teacher.lms.change_password');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password lama salah.');
                }
            }],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('teacher.lms.index')->with('success', 'Password berhasil diganti.');
    }

    protected function authorizeTeacher(ClassSession $classSession)
    {
        if ($classSession->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }
    }
}