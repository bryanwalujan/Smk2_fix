<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controller untuk mengelola absensi siswa dan guru.
 * Menangani CRUD absensi manual, scan barcode, dan validasi data.
 */
class AttendanceController extends Controller
{
    /**
     * Menampilkan daftar absensi berdasarkan tanggal dan tipe pengguna.
     *
     * @param Request $request Input berisi tanggal dan tipe (all, student, teacher)
     * @return \Illuminate\View\View Halaman index dengan data absensi
     */
    public function index(Request $request)
    {
        // Ambil tanggal dari input, default ke hari ini
        $date = $request->input('date', now()->toDateString());
        // Ambil tipe pengguna, default ke 'all'
        $type = $request->input('type', 'all');

        // Query absensi siswa
        $query = StudentAttendance::query()
            ->select(
                'student_attendances.id',
                'students.name as user_name',
                'student_attendances.tanggal',
                'student_attendances.waktu_masuk',
                'student_attendances.waktu_pulang',
                'student_attendances.status',
                'student_attendances.metode_absen',
                DB::raw("'student' as user_type")
            )
            ->join('students', 'student_attendances.student_id', '=', 'students.id')
            ->whereDate('student_attendances.tanggal', $date);

        // Tambahkan absensi guru jika tipe 'all' atau 'teacher'
        if ($type === 'all' || $type === 'teacher') {
            $teacherQuery = TeacherAttendance::query()
                ->select(
                    'teacher_attendances.id',
                    'teachers.name as user_name',
                    'teacher_attendances.tanggal',
                    'teacher_attendances.waktu_masuk',
                    'teacher_attendances.waktu_pulang',
                    'teacher_attendances.status',
                    'teacher_attendances.metode_absen',
                    DB::raw("'teacher' as user_type")
                )
                ->join('teachers', 'teacher_attendances.teacher_id', '=', 'teachers.id')
                ->whereDate('teacher_attendances.tanggal', $date);

            if ($type === 'all') {
                $query = $query->union($teacherQuery);
            } elseif ($type === 'teacher') {
                $query = $teacherQuery;
            }
        }

        // Ambil data dan urutkan berdasarkan tanggal dan waktu masuk
        $attendances = $query->orderBy('tanggal', 'desc')->orderBy('waktu_masuk', 'desc')->get();

        // Kembalikan view dengan data
        return view('admin.attendance.index', compact('attendances', 'date', 'type'));
    }

    /**
     * Menampilkan form untuk menambah absensi manual.
     *
     * @return \Illuminate\View\View Halaman create dengan daftar siswa dan guru
     */
    public function create()
    {
        // Ambil daftar siswa dan guru untuk dropdown
        $students = Student::all()->pluck('name', 'id');
        $teachers = Teacher::all()->pluck('name', 'id');

        return view('admin.attendance.create', compact('students', 'teachers'));
    }

    /**
     * Menyimpan absensi baru dari form manual.
     *
     * @param Request $request Data form absensi
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan
     */
    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'user_type' => 'required|in:student,teacher',
            'user_id' => 'required|integer',
            'tanggal' => 'required|date',
            'waktu_pulang' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'metode_absen' => 'required|in:manual,barcode',
        ]);

        try {
            $userType = $request->user_type;
            $userId = $request->user_id;
            $today = $request->tanggal;
            $currentTime = now()->toTimeString();

            // Ambil nama pengguna berdasarkan tipe
            $name = $userType === 'student' ? Student::findOrFail($userId)->name : Teacher::findOrFail($userId)->name;
            $model = $userType === 'student' ? StudentAttendance::class : TeacherAttendance::class;
            $idField = $userType === 'student' ? 'student_id' : 'teacher_id';

            // Cek apakah sudah ada absensi untuk pengguna dan tanggal ini
            $existingAttendance = $model::where($idField, $userId)
                ->whereDate('tanggal', $today)
                ->first();

            if ($existingAttendance) {
                return redirect()->route('attendance.index')->with('error', $name . ' sudah memiliki absensi untuk tanggal ini.');
            }

            // Validasi waktu pulang harus setelah waktu masuk
            if ($request->waktu_pulang) {
                $waktuMasuk = Carbon::parse($today . ' ' . $currentTime);
                $waktuPulang = Carbon::parse($today . ' ' . $request->waktu_pulang);
                if ($waktuPulang->lte($waktuMasuk)) {
                    return redirect()->route('attendance.index')->with('error', 'Waktu pulang harus setelah waktu masuk.');
                }
            }

            // Simpan absensi baru
            $model::create([
                $idField => $userId,
                'tanggal' => $today,
                'waktu_masuk' => $currentTime,
                'waktu_pulang' => $request->waktu_pulang,
                'status' => $request->status,
                'metode_absen' => $request->metode_absen,
            ]);

            return redirect()->route('attendance.index')->with('success', 'Absensi untuk ' . $name . ' berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating manual attendance: ' . $e->getMessage());
            return redirect()->route('attendance.index')->with('error', 'Terjadi kesalahan saat menambahkan absensi.');
        }
    }

    /**
     * Menampilkan form untuk mengedit absensi.
     *
     * @param int $id ID absensi
     * @param Request 
     * @return \Illuminate\View\View 
     */
    public function edit($id, Request $request)
    {
      
        $type = $request->query('type', 'student');

        // Ambil data absensi berdasarkan tipe
        $attendance = $type === 'student'
            ? StudentAttendance::findOrFail($id)
            : TeacherAttendance::findOrFail($id);

        // Ambil daftar siswa dan guru untuk dropdown
        $students = Student::all()->pluck('name', 'id');
        $teachers = Teacher::all()->pluck('name', 'id');

        return view('admin.attendance.edit', compact('attendance', 'type', 'students', 'teachers'));
    }

    /**
     *
     * @param Request 
     * @param int 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        Log::info('Update attendance request data: ', $request->all());

        $request->validate([
            'user_type' => 'required|in:student,teacher',
            'user_id' => 'required|integer',
            'tanggal' => 'required|date',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'waktu_pulang' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'metode_absen' => 'required|in:manual,barcode',
        ]);

        try {
            $userType = $request->user_type;
            $userId = $request->user_id;
            $model = $userType === 'student' ? StudentAttendance::class : TeacherAttendance::class;
            $idField = $userType === 'student' ? 'student_id' : 'teacher_id';

            $attendance = $model::findOrFail($id);

            $user = $userType === 'student'
                ? Student::find($userId)
                : Teacher::find($userId);
            if (!$user) {
                throw new \Exception("Pengguna dengan ID {$userId} tidak ditemukan untuk tipe {$userType}.");
            }
            $name = $user->name;

            $waktuMasuk = $request->filled('waktu_masuk') ? $request->waktu_masuk : (
                $attendance->waktu_masuk ? Carbon::parse($attendance->waktu_masuk)->format('H:i') : null
            );

            if (!$waktuMasuk) {
                throw new \Exception('Waktu masuk tidak boleh kosong.');
            }

            if ($request->waktu_pulang) {
                $waktuMasukCarbon = Carbon::parse($request->tanggal . ' ' . $waktuMasuk);
                $waktuPulangCarbon = Carbon::parse($request->tanggal . ' ' . $request->waktu_pulang);
                if ($waktuPulangCarbon->lte($waktuMasukCarbon)) {
                    return redirect()->route('attendance.index')->with('error', 'Waktu pulang harus setelah waktu masuk.');
                }
            }

            $attendance->update([
                $idField => $userId,
                'tanggal' => $request->tanggal,
                'waktu_masuk' => $waktuMasuk,
                'waktu_pulang' => $request->waktu_pulang,
                'status' => $request->status,
                'metode_absen' => $request->metode_absen,
            ]);

            return redirect()->route('attendance.index')->with('success', 'Absensi untuk ' . $name . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating attendance: ' . $e->getMessage() . ' | Data: ' . json_encode($request->all()));
            return redirect()->route('attendance.index')->with('error', 'Terjadi kesalahan saat memperbarui absensi: ' . $e->getMessage());
        }
    }

    /**
     * 
     *
     * @return \Illuminate\View\View
     */
    public function showScanPage()
    {
        return view('admin.attendance.scan');
    }

    /**
     * @param Request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse JSON untuk AJAX atau redirect
     */
    public function scanBarcode(Request $request)
    {

        $request->validate([
            'barcode' => 'required|string',
        ]);

        try {
            $barcode = $request->barcode;
            $today = now()->toDateString();
            $currentTime = now()->toTimeString();

            $student = Student::where('barcode', $barcode)->first();
            if ($student) {
                $result = $this->processAttendance(
                    StudentAttendance::class,
                    'student_id',
                    $student->id,
                    $today,
                    $currentTime,
                    $student->name
                );
                if ($request->expectsJson()) {
                    return response()->json($result);
                }
                return redirect()->route('attendance.index')->with($result['success'] ? 'success' : 'error', $result['message']);
            }

            $teacher = Teacher::where('barcode', $barcode)->first();
            if ($teacher) {
                $result = $this->processAttendance(
                    TeacherAttendance::class,
                    'teacher_id',
                    $teacher->id,
                    $today,
                    $currentTime,
                    $teacher->name
                );
                if ($request->expectsJson()) {
                    return response()->json($result);
                }
                return redirect()->route('attendance.index')->with($result['success'] ? 'success' : 'error', $result['message']);
            }

            $errorResult = [
                'success' => false,
                'message' => 'Barcode tidak valid atau tidak terdaftar',
            ];
            if ($request->expectsJson()) {
                return response()->json($errorResult, 400);
            }
            return redirect()->route('attendance.index')->with('error', $errorResult['message']);
        } catch (\Exception $e) {
            Log::error('Error scanning barcode: ' . $e->getMessage());
            $errorResult = [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ];
            if ($request->expectsJson()) {
                return response()->json($errorResult, 500);
            }
            return redirect()->route('attendance.index')->with('error', $errorResult['message']);
        }
    }

    /**
     * Memproses logika absensi untuk siswa atau guru.
     *
     * @param string $model Kelas model (StudentAttendance/TeacherAttendance)
     * @param string $idField Nama kolom ID (student_id/teacher_id)
     * @param int $id ID pengguna
     * @param string $today Tanggal hari ini
     * @param string $currentTime Waktu saat ini
     * @param string $name Nama pengguna
     * @return array Hasil proses absensi
     */
    private function processAttendance($model, $idField, $id, $today, $currentTime, $name)
    {
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