<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::all();
        Log::info('Classroom Index', [
            'classrooms' => $classrooms->map(function ($classroom) {
                return [
                    'id' => $classroom->id,
                    'level' => $classroom->level,
                    'major' => $classroom->major,
                    'class_code' => $classroom->class_code,
                    'full_name' => $classroom->full_name,
                ];
            })->toArray(),
        ]);
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

        $data = $request->only(['level', 'major', 'class_code']);
        $data['full_name'] = "{$data['level']} {$data['major']} {$data['class_code']}";

        $classroom = Classroom::create($data);

        Log::info('Classroom Created', [
            'id' => $classroom->id,
            'full_name' => $classroom->full_name,
            'level' => $classroom->level,
            'major' => $classroom->major,
            'class_code' => $classroom->class_code,
        ]);

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Classroom $classroom)
    {
        $classroom->load('students', 'schedules.teacher');
        Log::info('Classroom Show', [
            'id' => $classroom->id,
            'full_name' => $classroom->full_name,
            'students_count' => $classroom->students->count(),
            'schedules_count' => $classroom->schedules->count(),
        ]);
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

        $data = $request->only(['level', 'major', 'class_code']);
        $data['full_name'] = "{$data['level']} {$data['major']} {$data['class_code']}";

        $classroom->update($data);

        Log::info('Classroom Updated', [
            'id' => $classroom->id,
            'full_name' => $classroom->full_name,
            'level' => $classroom->level,
            'major' => $classroom->major,
            'class_code' => $classroom->class_code,
        ]);

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        try {
            $classroom->delete();
            Log::info('Classroom Deleted', [
                'id' => $classroom->id,
                'full_name' => $classroom->full_name,
            ]);
            return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Classroom Deletion Failed', [
                'id' => $classroom->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('classrooms.index')->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa atau jadwal.');
        }
    }
}