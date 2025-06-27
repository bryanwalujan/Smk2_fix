<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Subject;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    public function index()
    {
        $schedules = Schedule::with(['classroom', 'teacher', 'subject'])->get();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function create(Classroom $classroom)
    {
        $teachers = Teacher::all();
        $subjects = Subject::pluck('name', 'id');
        return view('admin.schedules.create', compact('classroom', 'subjects', 'teachers'));
    }

    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Cek jika hari adalah hari libur
        $dayMap = ['Senin' => 'Monday', 'Selasa' => 'Tuesday', 'Rabu' => 'Wednesday', 'Kamis' => 'Thursday', 'Jumat' => 'Friday'];
        $dateToCheck = Carbon::parse('next ' . $dayMap[$request->day]);
        if ($this->holidayService->isHoliday($dateToCheck)) {
            return redirect()->back()->with('error', 'Jadwal tidak dapat dibuat pada hari ' . $request->day . ' karena merupakan hari libur.');
        }

        Schedule::create([
            'classroom_id' => $classroom->id,
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Classroom $classroom, Schedule $schedule)
    {
        $teachers = Teacher::all();
        $subjects = Subject::pluck('name', 'id');
        return view('admin.schedules.edit', compact('classroom', 'schedule', 'subjects', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom, Schedule $schedule)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Cek jika hari adalah hari libur
        $dayMap = ['Senin' => 'Monday', 'Selasa' => 'Tuesday', 'Rabu' => 'Wednesday', 'Kamis' => 'Thursday', 'Jumat' => 'Friday'];
        $dateToCheck = Carbon::parse('next ' . $dayMap[$request->day]);
        if ($this->holidayService->isHoliday($dateToCheck)) {
            return redirect()->back()->with('error', 'Jadwal tidak dapat diperbarui pada hari ' . $request->day . ' karena merupakan hari libur.');
        }

        $schedule->update([
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom, Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('classrooms.show', $classroom)->with('success', 'Jadwal berhasil dihapus.');
    }
}