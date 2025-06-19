<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function create(Classroom $classroom)
    {
        $teachers = Teacher::all();
        $subjects = $classroom->teachers()->pluck('teacher_classroom_subject.subject_name')->unique();
        return view('admin.schedules.create', compact('classroom', 'subjects', 'teachers'));
    }

    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'subject_name' => 'required|string|max:100',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        Schedule::create([
            'classroom_id' => $classroom->id,
            'teacher_id' => $request->teacher_id,
            'subject_name' => $request->subject_name,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Jadwal berhasil ditambahkan.');
    }
}