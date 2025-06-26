<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::all();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('admin.classrooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'level' => 'required|in:10,11,12',
            'major' => 'required|string|max:50',
            'class_code' => 'required|string|max:10',
        ]);

        Classroom::create($request->only(['level', 'major', 'class_code']));

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Classroom $classroom)
    {
        $classroom->load('students', 'schedules.teacher');
        return view('admin.classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom)
    {
        return view('admin.classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'level' => 'required|in:10,11,12',
            'major' => 'required|string|max:50',
            'class_code' => 'required|string|max:10',
        ]);

        $classroom->update($request->only(['level', 'major', 'class_code']));

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        try {
            $classroom->delete();
            return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('classrooms.index')->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa atau jadwal.');
        }
    }
}