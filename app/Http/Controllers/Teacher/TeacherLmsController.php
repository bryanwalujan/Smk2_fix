<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Submission;
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
        $today = Carbon::today()->translatedFormat('l');
        $classSessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('day', $today)
            ->with(['classroom', 'subject'])
            ->get();
        $allClassSessions = ClassSession::where('teacher_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('start_time')
            ->get()
            ->map(function ($session) use ($today) {
                $session->is_today = $session->day === $today;
                return $session;
            });
        $uniqueSubjectsCount = ClassSession::where('teacher_id', $teacher->id)
            ->distinct('subject_id')
            ->count('subject_id');
        $subjects = ClassSession::where('teacher_id', $teacher->id)
            ->with(['subject', 'classroom'])
            ->get()
            ->pluck('subject.name', 'classroom_id')
            ->toArray();
        return view('teacher.lms.index', compact(
            'subjects',
            'classSessions',
            'allClassSessions',
            'uniqueSubjectsCount'
        ));
    }

    public function showSession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $classSession->load('materials', 'assignments.submissions');
        return view('teacher.lms.show_session', compact('classSession'));
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

        // Validate the request
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

        // Validate schedule_id
        if (!ClassSession::where('id', $classSession->id)->where('teacher_id', $teacher->id)->exists()) {
            return redirect()->route('teacher.lms.index')->with('error', 'Jadwal tidak valid atau tidak ditemukan.');
        }

        // Store Material
        $materialData = $request->material;
        $materialData['schedule_id'] = $classSession->id;
        if ($request->hasFile('material.file')) {
            $materialData['file_path'] = $request->file('material.file')->store('materials', 'public');
        }
        $material = Material::create($materialData);

        // Store Assignments
        if ($request->has('assignments')) {
            foreach ($request->assignments as $index => $assignmentData) {
                $assignmentData['schedule_id'] = $classSession->id;
                $assignmentData['material_id'] = $material->id; // Link assignment to material
                if ($request->hasFile("assignments.$index.file")) {
                    $assignmentData['file_path'] = $request->file("assignments.$index.file")->store('assignments', 'public');
                }
                Assignment::create($assignmentData);
            }
        }

        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Materi dan tugas berhasil ditambahkan.');
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
            'schedule_id' => $classSession->id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);
        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function showAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        $assignment->load(['submissions.student.user', 'submissions.student.classroom']);
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

    public function gradeSubmission(Request $request, Assignment $assignment, Submission $submission)
    {
        $this->authorizeTeacher($assignment->classSession);
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);
        $submission->update([
            'score' => $request->score,
        ]);
        return redirect()->route('teacher.lms.show_submissions', $assignment)->with('success', 'Nilai tugas berhasil diperbarui.');
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