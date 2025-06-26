<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\TeacherClassroomSubject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class TeacherLmsController extends Controller
{
    /**
     * Display the teacher's LMS dashboard with admin-created class schedules.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        // Get teacher's assigned subjects and classrooms
        $subjects = TeacherClassroomSubject::where('teacher_id', $teacher->id)
            ->pluck('subject_name', 'classroom_id')
            ->toArray();
        
        // Get today's classes (admin-created)
        $today = Carbon::today()->translatedFormat('l'); // e.g., "Senin"
        $classSessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->whereNotNull('created_by') // Ensure admin-created
            ->with('classroom')
            ->get();
        
        // Get all classes (admin-created)
        $allClassSessions = ClassSession::where('teacher_id', $teacher->id)
            ->whereNotNull('created_by')
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

    /**
     * Show a class session with its materials and assignments.
     *
     * @param ClassSession $classSession
     * @return \Illuminate\View\View
     */
    public function showSession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $classSession->load('materials', 'assignments.submissions');
        return view('teacher.lms.show_session', compact('classSession'));
    }

    /**
     * Show form to create a material for a class session.
     *
     * @param ClassSession $classSession
     * @return \Illuminate\View\View
     */
    public function createMaterial(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.create_material', compact('classSession'));
    }

    /**
     * Store a new material for a class session.
     *
     * @param Request $request
     * @param ClassSession $classSession
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Show a material for a class session.
     *
     * @param ClassSession $classSession
     * @param Material $material
     * @return \Illuminate\View\View
     */
    public function showMaterial(ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.show_material', compact('classSession', 'material'));
    }

    /**
     * Show form to edit a material.
     *
     * @param ClassSession $classSession
     * @param Material $material
     * @return \Illuminate\View\View
     */
    public function editMaterial(ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.edit_material', compact('classSession', 'material'));
    }

    /**
     * Update a material.
     *
     * @param Request $request
     * @param ClassSession $classSession
     * @param Material $material
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Delete a material.
     *
     * @param ClassSession $classSession
     * @param Material $material
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMaterial(ClassSession $classSession, Material $material)
    {
        $this->authorizeTeacher($classSession);
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Show form to create an assignment for a class session.
     *
     * @param ClassSession $classSession
     * @return \Illuminate\View\View
     */
    public function createAssignment(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.create_assignment', compact('classSession'));
    }

    /**
     * Store a new assignment.
     *
     * @param Request $request
     * @param ClassSession $classSession
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Show an assignment with its submissions.
     *
     * @param ClassSession $classSession
     * @param Assignment $assignment
     * @return \Illuminate\View\View
     */
    public function showAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        $assignment->load(['submissions.student.user', 'submissions.student.classroom']);
        return view('teacher.lms.show_assignment', compact('classSession', 'assignment'));
    }

    /**
     * Show form to edit an assignment.
     *
     * @param ClassSession $classSession
     * @param Assignment $assignment
     * @return \Illuminate\View\View
     */
    public function editAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        return view('teacher.lms.edit_assignment', compact('classSession', 'assignment'));
    }

    /**
     * Update an assignment.
     *
     * @param Request $request
     * @param ClassSession $classSession
     * @param Assignment $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Delete an assignment.
     *
     * @param ClassSession $classSession
     * @param Assignment $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAssignment(ClassSession $classSession, Assignment $assignment)
    {
        $this->authorizeTeacher($classSession);
        $assignment->delete();
        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Show submissions for an assignment and allow grading.
     *
     * @param Assignment $assignment
     * @return \Illuminate\View\View
     */
    public function showSubmissions(Assignment $assignment)
    {
        $this->authorizeTeacher($assignment->classSession);
        $assignment->load(['submissions.student.user', 'submissions.student.classroom', 'classSession']);
        return view('teacher.lms.show_submissions', compact('assignment'));
    }

    /**
     * Grade a student submission.
     *
     * @param Request $request
     * @param Assignment $assignment
     * @param Submission $submission
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Show the change password form.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('teacher.lms.change_password');
    }

    /**
     * Update the teacher's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Authorize teacher access to a class session.
     *
     * @param ClassSession $classSession
     * @return void
     */
    protected function authorizeTeacher(ClassSession $classSession)
    {
        if ($classSession->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }
    }
}