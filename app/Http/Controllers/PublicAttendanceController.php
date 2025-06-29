<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\StudentAttendanceNotification;

class PublicAttendanceController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Menampilkan halaman scan barcode untuk publik.
     *
     * @return \Illuminate\View\View
     */
    public function scan()
    {
        if ($this->holidayService->isHoliday(now())) {
            return redirect()->route('welcome')->with('error', 'Tidak dapat melakukan scan absensi pada hari libur.');
        }

        return view('scan.scan');
    }

    /**
     * Memproses scan barcode untuk absensi siswa atau guru.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        try {
            if ($this->holidayService->isHoliday(now())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mencatat absensi pada hari libur.'
                ], 403);
            }

            $barcode = $request->barcode;
            $today = now()->toDateString();
            $currentTime = now()->format('H:i');

            // Cek apakah barcode milik siswa
            $student = Student::where('barcode', $barcode)->first();
            if ($student) {
                Log::info('Siswa ditemukan: ' . $student->name . ', Barcode: ' . $barcode . ', Email: ' . ($student->user ? $student->user->email : 'kosong'));
                return response()->json($this->processStudentAttendance(
                    StudentAttendance::class,
                    'student_id',
                    $student->id,
                    $today,
                    $currentTime,
                    $student->name,
                    $student->user ? $student->user->email : null
                ));
            }

            // Cek apakah barcode milik guru
            $teacher = Teacher::where('barcode', $barcode)->first();
            if ($teacher) {
                Log::info('Guru ditemukan: ' . $teacher->name . ', Barcode: ' . $barcode);
                return response()->json($this->processTeacherAttendance(
                    TeacherAttendance::class,
                    'teacher_id',
                    $teacher->id,
                    $today,
                    $currentTime,
                    $teacher->name
                ));
            }

            // Barcode tidak ditemukan
            Log::warning('Barcode tidak ditemukan: ' . $barcode);
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak valid atau tidak terdaftar'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error scanning barcode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memproses absensi siswa (satu kali per hari).
     *
     * @param string $model
     * @param string $idField
     * @param int $id
     * @param string $today
     * @param string $currentTime
     * @param string $name
     * @param string|null $parentEmail
     * @return array
     */
    private function processStudentAttendance($model, $idField, $id, $today, $currentTime, $name, $parentEmail = null)
    {
        Log::info('Memproses absensi untuk siswa: ' . $name . ', Email: ' . ($parentEmail ?: 'kosong'));
        $existingAttendance = $model::where($idField, $id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingAttendance) {
            Log::info('Absensi sudah ada untuk siswa: ' . $name);
            return [
                'success' => false,
                'message' => $name . ' sudah absen hari ini.',
                'type' => 'already_done',
                'name' => $name,
                'time' => $currentTime,
            ];
        }

        $attendance = $model::create([
            $idField => $id,
            'tanggal' => $today,
            'waktu_masuk' => $currentTime,
            'status' => 'hadir',
            'metode_absen' => 'barcode',
        ]);

        // Kirim notifikasi email ke email orang tua
        if ($parentEmail) {
            try {
                $student = Student::findOrFail($id);
                Log::info('Mengirim email ke: ' . $parentEmail);
                Mail::to($parentEmail)->send(new StudentAttendanceNotification($student, $attendance));
                Log::info('Email berhasil dikirim ke: ' . $parentEmail);
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email absensi ke ' . $parentEmail . ': ' . $e->getMessage());
            }
        } else {
            Log::warning('Tidak ada email orang tua untuk siswa: ' . $name);
        }

        return [
            'success' => true,
            'message' => 'Absensi ' . $name . ' berhasil dicatat.',
            'type' => 'check_in',
            'name' => $name,
            'time' => $currentTime,
        ];
    }

    /**
     * Memproses absensi guru (mengikuti logika masuk dan pulang).
     *
     * @param string $model
     * @param string $idField
     * @param int $id
     * @param string $today
     * @param string $currentTime
     * @param string $name
     * @return array
     */
    private function processTeacherAttendance($model, $idField, $id, $today, $currentTime, $name)
    {
        Log::info('Memproses absensi untuk guru: ' . $name);
        $existingAttendance = $model::where($idField, $id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingAttendance) {
            if ($existingAttendance->waktu_pulang) {
                return [
                    'success' => false,
                    'message' => $name . ' sudah melakukan absen hari ini',
                    'type' => 'already_done',
                    'name' => $name,
                    'time' => $currentTime
                ];
            }

            $checkInTime = Carbon::parse($existingAttendance->waktu_masuk);
            $currentTimeCarbon = Carbon::parse($currentTime);
            $minutesDifference = $checkInTime->diffInMinutes($currentTimeCarbon);

            if ($minutesDifference < 60) {
                return [
                    'success' => false,
                    'message' => $name . ', absen pulang terlalu cepat. Harus menunggu minimal 1 jam setelah absen masuk.',
                    'type' => 'too_soon',
                    'name' => $name,
                    'time' => $currentTime
                ];
            }

            $existingAttendance->update([
                'waktu_pulang' => $currentTime,
                'status' => 'hadir',
                'metode_absen' => 'barcode'
            ]);

            return [
                'success' => true,
                'message' => 'Absensi pulang ' . $name . ' berhasil',
                'type' => 'check_out',
                'name' => $name,
                'time' => $currentTime
            ];
        }

        $model::create([
            $idField => $id,
            'tanggal' => $today,
            'waktu_masuk' => $currentTime,
            'status' => 'hadir',
            'metode_absen' => 'barcode'
        ]);

        return [
            'success' => true,
            'message' => 'Absensi masuk ' . $name . ' berhasil',
            'type' => 'check_in',
            'name' => $name,
            'time' => $currentTime
        ];
    }
}
