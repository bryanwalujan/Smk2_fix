<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeacherLmsController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        $subjects = $teacher->classrooms()->pluck('teacher_classroom_subject.subject_name', 'classrooms.id')->toArray();
        $classSessions = $teacher->classSessions()->with(['classroom', 'materials', 'assignments'])->get();
        return view('teacher.lms.index', compact('subjects', 'classSessions'));
    }

    public function createSession()
    {
        $teacher = Auth::user()->teacher;
        $subjects = $teacher->classrooms()->pluck('teacher_classroom_subject.subject_name', 'classrooms.id')->toArray();
        return view('teacher.lms.create_session', compact('subjects'));
    }

    public function storeSession(Request $request)
    {
        \Log::info('Request Data:', $request->all());
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_name' => 'required|string|max:100',
            'title' => 'required|string|max:100',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $teacher = Auth::user()->teacher;
        if (!$teacher->classrooms()->where('classrooms.id', $request->classroom_id)->where('teacher_classroom_subject.subject_name', $request->subject_name)->exists()) {
            return back()->withErrors(['subject_name' => 'Mata pelajaran tidak terkait dengan kelas ini.']);
        }

        ClassSession::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $request->classroom_id,
            'subject_name' => $request->subject_name,
            'title' => $request->title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('teacher.lms.index')->with('success', 'Sesi kelas berhasil ditambahkan.');
    }

    public function editSession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $teacher = Auth::user()->teacher;
        $subjects = $teacher->classrooms()->pluck('teacher_classroom_subject.subject_name', 'classrooms.id')->toArray();
        return view('teacher.lms.edit_session', compact('classSession', 'subjects'));
    }

    public function updateSession(Request $request, ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        \Log::info('Update Request Data:', $request->all());
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_name' => 'required|string|max:100',
            'title' => 'required|string|max:100',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $teacher = Auth::user()->teacher;
        if (!$teacher->classrooms()->where('classrooms.id', $request->classroom_id)->where('teacher_classroom_subject.subject_name', $request->subject_name)->exists()) {
            return back()->withErrors(['subject_name' => 'Mata pelajaran tidak terkait dengan kelas ini.']);
        }

        $classSession->update([
            'classroom_id' => $request->classroom_id,
            'subject_name' => $request->subject_name,
            'title' => $request->title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('teacher.lms.index')->with('success', 'Sesi kelas berhasil diperbarui.');
    }

    public function showSession(ClassSession $classSession)
    {
        $this->authorizeTeacher($classSession);
        $classSession->load(['materials', 'assignments.submissions.student']);
        return view('teacher.lms.show_session', compact('classSession'));
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
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = [
            'class_session_id' => $classSession->id,
            'title' => $request->title,
            'content' => $request->content,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        Material::create($data);

        return redirect()->route('teacher.lms.show_session', $classSession)->with('success', 'Materi berhasil ditambahkan.');
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

    protected function authorizeTeacher(ClassSession $classSession)
    {
        if ($classSession->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }
    }
}