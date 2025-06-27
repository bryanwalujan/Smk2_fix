<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\StudentAttendanceNotification;

/**
 * Controller untuk mengelola absensi siswa dan guru.
 * Menangani CRUD absensi manual, scan barcode, dan validasi data.
 */
class AttendanceController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Menampilkan daftar absensi berdasarkan tanggal dan tipe pengguna.
     *
     * @param Request $request Input berisi tanggal dan tipe (all, student, teacher)
     * @return \Illuminate\View\View Halaman index dengan data absensi
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $type = $request->input('type', 'all');

        if ($this->holidayService->isHoliday(Carbon::parse($date))) {
            return view('admin.attendance.index', [
                'attendances' => collect(),
                'date' => $date,
                'type' => $type,
                'isHoliday' => true,
                'holidayMessage' => 'Tanggal ini adalah hari libur (akhir pekan atau hari libur nasional).'
            ]);
        }

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

        $attendances = $query->orderBy('tanggal', 'desc')->orderBy('waktu_masuk', 'desc')->get();

        return view('admin.attendance.index', compact('attendances', 'date', 'type'));
    }

    /**
     * Menampilkan form untuk menambah absensi manual.
     *
     * @return \Illuminate\View\View Halaman create dengan daftar siswa dan guru
     */
    public function create()
    {
        if ($this->holidayService->isHoliday(now())) {
            return redirect()->route('attendance.index')->with('error', 'Tidak dapat membuat absensi pada hari libur.');
        }

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
        $request->validate([
            'user_type' => 'required|in:student,teacher',
            'user_id' => 'required|integer',
            'tanggal' => 'required|date',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'waktu_pulang' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,tidak_hadir,izin,sakit',
            'metode_absen' => 'required|in:manual,barcode',
        ]);

        try {
            if ($this->holidayService->isHoliday(Carbon::parse($request->tanggal))) {
                return redirect()->route('attendance.index')->with('error', 'Tidak dapat mencatat absensi pada hari libur.');
            }

            $userType = $request->user_type;
            $userId = $request->user_id;
            $today = $request->tanggal;
            $name = $userType === 'student' ? Student::findOrFail($userId)->name : Teacher::findOrFail($userId)->name;
            $model = $userType === 'student' ? StudentAttendance::class : TeacherAttendance::class;
            $idField = $userType === 'student' ? 'student_id' : 'teacher_id';

            $existingAttendance = $model::where($idField, $userId)
                ->whereDate('tanggal', $today)
                ->first();

            if ($existingAttendance) {
                return redirect()->route('attendance.index')->with('error', $name . ' sudah memiliki absensi untuk tanggal ini.');
            }

            if ($request->waktu_pulang && $request->waktu_masuk) {
                $waktuMasuk = Carbon::parse($today . ' ' . $request->waktu_masuk);
                $waktuPulang = Carbon::parse($today . ' ' . $request->waktu_pulang);
                if ($waktuPulang->lte($waktuMasuk)) {
                    return redirect()->route('attendance.index')->with('error', 'Waktu pulang harus setelah waktu masuk.');
                }
            }

            $attendance = $model::create([
                $idField => $userId,
                'tanggal' => $today,
                'waktu_masuk' => $request->waktu_masuk ?? now()->format('H:i'),
                'waktu_pulang' => $request->waktu_pulang,
                'status' => $request->status,
                'metode_absen' => $request->metode_absen,
            ]);

            // Kirim notifikasi email untuk absensi siswa (hanya untuk absen masuk)
            if ($userType === 'student' && $request->status === 'hadir') {
                $student = Student::findOrFail($userId);
                $parentEmail = $student->user ? $student->user->email : null;
                Log::info('Memproses absensi manual untuk siswa: ' . $student->name . ', Email: ' . ($parentEmail ?: 'kosong'));
                if ($parentEmail) {
                    try {
                        Mail::to($parentEmail)->send(new StudentAttendanceNotification($student, $attendance));
                        Log::info('Email berhasil dikirim ke: ' . $parentEmail);
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email absensi ke ' . $parentEmail . ': ' . $e->getMessage());
                    }
                } else {
                    Log::warning('Tidak ada email orang tua untuk siswa: ' . $student->name);
                }
            }

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
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $type = $request->query('type', 'student');

        $attendance = $type === 'student'
            ? StudentAttendance::findOrFail($id)
            : TeacherAttendance::findOrFail($id);

        if ($this->holidayService->isHoliday($attendance->tanggal)) {
            return redirect()->route('attendance.index')->with('error', 'Tidak dapat mengedit absensi pada hari libur.');
        }

        $students = Student::all()->pluck('name', 'id');
        $teachers = Teacher::all()->pluck('name', 'id');

        return view('admin.attendance.edit', compact('attendance', 'type', 'students', 'teachers'));
    }

    /**
     * Memperbarui absensi.
     *
     * @param Request $request
     * @param int $id
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
            'status' => 'required|in:hadir,tidak_hadir,izin,sakit',
            'metode_absen' => 'required|in:manual,barcode',
        ]);

        try {
            if ($this->holidayService->isHoliday(Carbon::parse($request->tanggal))) {
                return redirect()->route('attendance.index')->with('error', 'Tidak dapat memperbarui absensi pada hari libur.');
            }

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

            if ($request->waktu_pulang && $waktuMasuk) {
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
     * Menampilkan halaman scan barcode.
     *
     * @return \Illuminate\View\View
     */
    public function showScanPage()
    {
        if ($this->holidayService->isHoliday(now())) {
            return redirect()->route('attendance.index')->with('error', 'Tidak dapat melakukan scan absensi pada hari libur.');
        }

        return view('admin.attendance.scan');
    }

    /**
     * Memproses scan barcode untuk absensi.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        try {
            if ($this->holidayService->isHoliday(now())) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Tidak dapat mencatat absensi pada hari libur.'], 403)
                    : redirect()->route('attendance.index')->with('error', 'Tidak dapat mencatat absensi pada hari libur.');
            }

            $barcode = $request->barcode;
            $today = now()->toDateString();
            $currentTime = now()->format('H:i');

            $student = Student::where('barcode', $barcode)->first();
            if ($student) {
                Log::info('Siswa ditemukan: ' . $student->name . ', Barcode: ' . $barcode . ', Email: ' . ($student->user ? $student->user->email : 'kosong'));
                $result = $this->processStudentAttendance(
                    StudentAttendance::class,
                    'student_id',
                    $student->id,
                    $today,
                    $currentTime,
                    $student->name,
                    $student->user ? $student->user->email : null // Ambil email dari tabel users
                );
                return $request->expectsJson()
                    ? response()->json($result)
                    : redirect()->route('attendance.index')->with($result['success'] ? 'success' : 'error', $result['message']);
            }

            $teacher = Teacher::where('barcode', $barcode)->first();
            if ($teacher) {
                Log::info('Guru ditemukan: ' . $teacher->name . ', Barcode: ' . $barcode);
                $result = $this->processTeacherAttendance(
                    TeacherAttendance::class,
                    'teacher_id',
                    $teacher->id,
                    $today,
                    $currentTime,
                    $teacher->name
                );
                return $request->expectsJson()
                    ? response()->json($result)
                    : redirect()->route('attendance.index')->with($result['success'] ? 'success' : 'error', $result['message']);
            }

            Log::warning('Barcode tidak ditemukan: ' . $barcode);
            $errorResult = [
                'success' => false,
                'message' => 'Barcode tidak valid atau tidak terdaftar',
            ];
            return $request->expectsJson()
                ? response()->json($errorResult, 400)
                : redirect()->route('attendance.index')->with('error', $errorResult['message']);
        } catch (\Exception $e) {
            Log::error('Error scanning barcode: ' . $e->getMessage());
            $errorResult = [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ];
            return $request->expectsJson()
                ? response()->json($errorResult, 500)
                : redirect()->route('attendance.index')->with('error', $errorResult['message']);
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
     * Memproses absensi guru (mengikuti logika lama: masuk dan pulang).
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