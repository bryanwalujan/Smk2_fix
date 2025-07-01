<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\ClassSession;
use App\Models\Teacher;
use App\Models\Subject;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $schedule = Schedule::create([
            'classroom_id' => $classroom->id,
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        $this->createRecurringClassSessions($schedule);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Jadwal berhasil ditambahkan dan pertemuan berulang dibuat.');
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
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $schedule->classSessions()->delete();

        $schedule->update([
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        $this->createRecurringClassSessions($schedule);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Jadwal berhasil diperbarui dan pertemuan berulang dibuat.');
    }

    public function destroy(Classroom $classroom, Schedule $schedule)
    {
        try {
            Log::info('Attempting to delete schedule', ['schedule_id' => $schedule->id]);
            $schedule->classSessions()->delete();
            Log::info('Deleted class_sessions for schedule_id: ' . $schedule->id);
            $schedule->delete();
            Log::info('Deleted schedule', ['schedule_id' => $schedule->id]);
            return redirect()->route('classrooms.show', $classroom)->with('success', 'Jadwal dan semua pertemuan terkait berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting schedule', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('classrooms.show', $classroom)->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function showSessions(Schedule $schedule)
    {
        $classSessions = ClassSession::where('teacher_id', $schedule->teacher_id)
            ->where('classroom_id', $schedule->classroom_id)
            ->where('subject_id', $schedule->subject_id)
            ->where('day_of_week', $schedule->day)
            ->where('start_time', $schedule->start_time)
            ->where('end_time', $schedule->end_time)
            ->orderBy('date')
            ->get();

        return view('admin.schedules.sessions', compact('schedule', 'classSessions'));
    }

    public function updateFirstSession(Request $request, Schedule $schedule)
    {
        $request->validate([
            'first_session_date' => 'required|date|after_or_equal:today',
        ]);

        try {
            $newFirstDate = Carbon::parse($request->first_session_date);
            $dayMap = [
                'Senin' => 'Monday',
                'Selasa' => 'Tuesday',
                'Rabu' => 'Wednesday',
                'Kamis' => 'Thursday',
                'Jumat' => 'Friday',
                'Sabtu' => 'Saturday'
            ];

            // Pastikan tanggal pertama sesuai dengan hari jadwal
            if ($newFirstDate->translatedFormat('l') !== $schedule->day) {
                return redirect()->route('admin.schedules.sessions', $schedule)
                    ->with('error', 'Tanggal pertama harus sesuai dengan hari jadwal (' . $schedule->day . ').');
            }

            // Hapus semua class_sessions untuk jadwal ini
            $schedule->classSessions()->delete();
            Log::info('Deleted existing class sessions for schedule', [
                'schedule_id' => $schedule->id,
            ]);

            // Buat ulang class_sessions mulai dari tanggal baru
            $totalWeeks = 52;
            $endDate = Carbon::create(2026, 6, 30);
            $currentDate = $newFirstDate;
            $sessionsCreated = 0;

            while ($sessionsCreated < $totalWeeks && $currentDate <= $endDate) {
                if (!$this->holidayService->isHoliday($currentDate)) {
                    ClassSession::create([
                        'teacher_id' => $schedule->teacher_id,
                        'classroom_id' => $schedule->classroom_id,
                        'subject_id' => $schedule->subject_id,
                        'day_of_week' => $schedule->day,
                        'date' => $currentDate->toDateString(),
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'created_by' => Auth::id(),
                    ]);
                    $sessionsCreated++;
                    Log::info('Created class session', [
                        'schedule_id' => $schedule->id,
                        'date' => $currentDate->toDateString(),
                        'day_of_week' => $schedule->day,
                    ]);
                } else {
                    Log::info('Skipped class session due to holiday', [
                        'schedule_id' => $schedule->id,
                        'date' => $currentDate->toDateString(),
                        'day_of_week' => $schedule->day,
                    ]);
                }
                $currentDate->addWeek();
            }

            Log::info('Finished creating recurring class sessions', [
                'schedule_id' => $schedule->id,
                'total_sessions' => $sessionsCreated,
            ]);

            return redirect()->route('admin.schedules.sessions', $schedule)
                ->with('success', 'Tanggal pertemuan pertama berhasil diubah dan pertemuan berulang dibuat.');
        } catch (\Exception $e) {
            Log::error('Error updating first session date', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.schedules.sessions', $schedule)
                ->with('error', 'Gagal mengubah tanggal pertemuan pertama: ' . $e->getMessage());
        }
    }

    public function deleteSession(Schedule $schedule, ClassSession $session)
    {
        try {
            // Pastikan session terkait dengan schedule
            if ($session->teacher_id !== $schedule->teacher_id ||
                $session->classroom_id !== $schedule->classroom_id ||
                $session->subject_id !== $schedule->subject_id ||
                $session->day_of_week !== $schedule->day) {
                return redirect()->route('admin.schedules.sessions', $schedule)
                    ->with('error', 'Pertemuan tidak sesuai dengan jadwal.');
            }

            // Hapus session ini dan semua session berikutnya
            ClassSession::where('teacher_id', $schedule->teacher_id)
                ->where('classroom_id', $schedule->classroom_id)
                ->where('subject_id', $schedule->subject_id)
                ->where('day_of_week', $schedule->day)
                ->where('start_time', $schedule->start_time)
                ->where('end_time', $schedule->end_time)
                ->where('date', '>=', $session->date)
                ->delete();

            Log::info('Deleted class session and subsequent sessions', [
                'schedule_id' => $schedule->id,
                'session_id' => $session->id,
                'date' => $session->date,
            ]);

            return redirect()->route('admin.schedules.sessions', $schedule)
                ->with('success', 'Pertemuan dan semua pertemuan berikutnya berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting class session', [
                'schedule_id' => $schedule->id,
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.schedules.sessions', $schedule)
                ->with('error', 'Gagal menghapus pertemuan: ' . $e->getMessage());
        }
    }

    protected function createRecurringClassSessions(Schedule $schedule)
    {
        $dayMap = [
            'Senin' => 'Monday',
            'Selasa' => 'Tuesday',
            'Rabu' => 'Wednesday',
            'Kamis' => 'Thursday',
            'Jumat' => 'Friday',
            'Sabtu' => 'Saturday'
        ];

        $totalWeeks = 52;
        $startDate = Carbon::today()->startOfWeek()->next($dayMap[$schedule->day]);
        $endDate = Carbon::create(2026, 6, 30);

        $currentDate = $startDate;
        $sessionsCreated = 0;
        while ($sessionsCreated < $totalWeeks && $currentDate <= $endDate) {
            if (!$this->holidayService->isHoliday($currentDate)) {
                ClassSession::create([
                    'teacher_id' => $schedule->teacher_id,
                    'classroom_id' => $schedule->classroom_id,
                    'subject_id' => $schedule->subject_id,
                    'day_of_week' => $schedule->day,
                    'date' => $currentDate->toDateString(),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'created_by' => Auth::id(),
                ]);
                $sessionsCreated++;
                Log::info('Created class session', [
                    'schedule_id' => $schedule->id,
                    'date' => $currentDate->toDateString(),
                    'day_of_week' => $schedule->day,
                ]);
            } else {
                Log::info('Skipped class session due to holiday', [
                    'schedule_id' => $schedule->id,
                    'date' => $currentDate->toDateString(),
                    'day_of_week' => $schedule->day,
                ]);
            }
            $currentDate->addWeek();
        }

        Log::info('Finished creating recurring class sessions', [
            'schedule_id' => $schedule->id,
            'total_sessions' => $sessionsCreated,
        ]);
    }
}