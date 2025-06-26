<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Exports\StudentsExport;
use App\Exports\TeachersExport;
use App\Exports\ClassroomsExport;
use App\Exports\SubjectsExport;
use App\Exports\AttendanceExport;
use App\Services\HolidayService;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class AdminController extends Controller
{
    use AuthorizesRequests;

    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    public function dashboard()
    {
        $roles = Role::whereIn('name', ['teacher', 'student'])->get();
        $permissions = Permission::all();
        return view('admin.dashboard', compact('roles', 'permissions'));
    }

    public function togglePermission(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        try {
            $validated = $request->validate([
                'role' => 'required|string|in:teacher,student',
                'permission' => 'required|string|exists:permissions,name',
            ]);

            if (!auth()->user()->can('manage_roles')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengelola role.'
                ], 403);
            }

            $role = Role::where('name', $validated['role'])->first();
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => "Role '{$validated['role']}' tidak ditemukan."
                ], 404);
            }

            $permission = Permission::where('name', $validated['permission'])->first();
            if (!$permission) {
                return response()->json([
                    'success' => false,
                    'message' => "Permission '{$validated['permission']}' tidak ditemukan."
                ], 404);
            }

            if ($role->hasPermissionTo($permission)) {
                $role->revokePermissionTo($permission);
                $message = "Izin '{$permission->name}' berhasil dihapus dari role {$role->name}.";
                $action = 'revoked';
            } else {
                $role->givePermissionTo($permission);
                $message = "Izin '{$permission->name}' berhasil ditambahkan ke role {$role->name}.";
                $action = 'granted';
            }

            Log::info('Permission toggled', [
                'user_id' => auth()->id(),
                'role' => $role->name,
                'permission' => $permission->name,
                'action' => $action
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'action' => $action,
                'role' => $role->name,
                'permission' => $permission->name
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed in togglePermission', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Data yang dikirim tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to toggle permission', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportStudentsTemplate()
    {
        $this->authorize('export_excel');
        $headings = ['NIS', 'Nama', 'Email', 'Tingkat', 'Jurusan', 'Kode Kelas'];
        $data = [
            ['1234567890', 'John Doe', 'john@example.com', '12', 'RPL', 'A'],
        ];

        return Excel::download(new class ($headings, $data) implements FromArray, WithHeadings {
            protected $headings;
            protected $data;

            public function __construct(array $headings, array $data)
            {
                $this->headings = $headings;
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        }, 'students_template.xlsx');
    }

    public function importStudents(Request $request)
    {
        $this->authorize('manage_users');

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ], [
            'file.required' => 'Harap unggah file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat Excel (.xlsx).',
        ]);

        try {
            $rows = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array)
                {
                    return $array;
                }
            }, $request->file('file'))[0];

            $errors = [];
            foreach (array_slice($rows, 1) as $index => $row) {
                if (count($row) < 6) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Mohon lengkapi semua kolom, termasuk NIS, Nama, Email, Tingkat, Jurusan, dan Kode Kelas.";
                    continue;
                }

                $nis = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $email = trim($row[2] ?? '');
                $level = trim($row[3] ?? '');
                $major = trim($row[4] ?? '');
                $classCode = trim($row[5] ?? '');

                if (empty($nis) || empty($name) || empty($email) || empty($level) || empty($major) || empty($classCode)) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Ada kolom yang kosong. Pastikan semua kolom diisi dengan benar.";
                    continue;
                }

                $classroom = Classroom::where('level', $level)
                    ->where('major', $major)
                    ->where('class_code', $classCode)
                    ->first();

                if (!$classroom) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Kelas '$level $major $classCode' belum terdaftar. Harap masukkan data kelas terlebih dahulu di menu Kelas.";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Email '$email' tidak valid. Mohon masukkan email yang benar.";
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Email '$email' sudah digunakan. Gunakan email lain.";
                    continue;
                }

                if (Student::where('nis', $nis)->exists()) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": NIS '$nis' sudah terdaftar. Gunakan NIS lain.";
                    continue;
                }

                DB::beginTransaction();

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($nis),
                    'role' => 'student',
                ]);

                $user->assignRole('student');

                $barcodeId = rand(100000, 999999);
                Student::create([
                    'nis' => $nis,
                    'name' => $name,
                    'barcode' => $barcodeId,
                    'user_id' => $user->id,
                    'classroom_id' => $classroom->id,
                ]);

                $qrCodeDir = public_path('qrcodes');
                if (!File::exists($qrCodeDir)) {
                    File::makeDirectory($qrCodeDir, 0755, true);
                }
                if (!is_writable($qrCodeDir)) {
                    throw new \Exception('Direktori qrcodes tidak memiliki izin tulis.');
                }
                QrCode::format('svg')
                    ->size(400)
                    ->margin(3)
                    ->errorCorrection('H')
                    ->color(40, 40, 40)
                    ->backgroundColor(245, 245, 245)
                    ->generate((string) $barcodeId, public_path('qrcodes/student_' . $barcodeId . '.svg'));

                DB::commit();
            }

            if (!empty($errors)) {
                return redirect()->back()->with('errors', $errors);
            }

            return redirect()->back()->with('success', 'Data siswa berhasil diimpor.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing students: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal mengimpor data siswa. Silakan coba lagi atau hubungi admin.');
        }
    }

    public function exportTeachersTemplate()
    {
        $this->authorize('export_excel');
        $headings = ['NIP', 'Nama', 'Email', 'Mata Pelajaran', 'Tingkat', 'Jurusan', 'Kode Kelas'];
        $data = [
            ['123456789', 'Jane Doe', 'jane@example.com', 'Matematika, Bahasa Inggris', '12', 'RPL', 'A'],
        ];

        return Excel::download(new class ($headings, $data) implements FromArray, WithHeadings {
            protected $headings;
            protected $data;

            public function __construct(array $headings, array $data)
            {
                $this->headings = $headings;
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        }, 'teachers_template.xlsx');
    }

    public function importTeachers(Request $request)
    {
        $this->authorize('manage_users');

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ], [
            'file.required' => 'Harap unggah file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat Excel (.xlsx).',
        ]);

        try {
            $rows = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array)
                {
                    return $array;
                }
            }, $request->file('file'))[0];

            $errors = [];
            foreach (array_slice($rows, 1) as $index => $row) {
                if (count($row) < 7) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Mohon lengkapi semua kolom, termasuk NIP, Nama, Email, Mata Pelajaran, Tingkat, Jurusan, dan Kode Kelas.";
                    continue;
                }

                $nip = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $email = trim($row[2] ?? '');
                $subjectsInput = trim($row[3] ?? '');
                $level = trim($row[4] ?? '');
                $major = trim($row[5] ?? '');
                $classCode = trim($row[6] ?? '');

                $classroom = null;
                if (!empty($level) || !empty($major) || !empty($classCode)) {
                    if (empty($level) || empty($major) || empty($classCode)) {
                        $errors[] = "Baris ke-" . ($index + 2) . ": Jika ingin menambahkan kelas, pastikan Tingkat, Jurusan, dan Kode Kelas diisi lengkap.";
                        continue;
                    }

                    $classroom = Classroom::where('level', $level)
                        ->where('major', $major)
                        ->where('class_code', $classCode)
                        ->first();

                    if (!$classroom) {
                        $errors[] = "Baris ke-" . ($index + 2) . ": Kelas '$level $major $classCode' belum terdaftar. Harap masukkan data kelas terlebih dahulu di menu Kelas.";
                        continue;
                    }
                }

                if (empty($nip) || empty($name) || empty($email) || empty($subjectsInput)) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Ada kolom yang kosong. Pastikan NIP, Nama, Email, dan Mata Pelajaran diisi.";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Email '$email' tidak valid. Mohon masukkan email yang benar.";
                    continue;
                }

                $subjectNames = array_filter(array_map('trim', explode(',', $subjectsInput)));
                $subjectIds = [];
                foreach ($subjectNames as $subjectName) {
                    $subject = Subject::where('name', $subjectName)->first();
                    if (!$subject) {
                        $errors[] = "Baris ke-" . ($index + 2) . ": Mata pelajaran '$subjectName' tidak ditemukan. Harap tambahkan mata pelajaran di menu Mata Pelajaran.";
                        continue 2;
                    }
                    $subjectIds[] = $subject->id;
                }

                if (empty($subjectIds)) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Tidak ada mata pelajaran yang valid. Pastikan nama mata pelajaran sesuai.";
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Email '$email' sudah digunakan. Gunakan email lain.";
                    continue;
                }

                if (Teacher::where('nip', $nip)->exists()) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": NIP '$nip' sudah terdaftar. Gunakan NIP lain.";
                    continue;
                }

                DB::beginTransaction();

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($nip),
                    'role' => 'teacher',
                ]);

                $user->assignRole('teacher');

                $barcodeId = rand(100000, 999999);
                $teacher = Teacher::create([
                    'nip' => $nip,
                    'name' => $name,
                    'barcode' => $barcodeId,
                    'user_id' => $user->id,
                    'classroom_id' => $classroom ? $classroom->id : null,
                ]);

                $teacher->subjects()->sync($subjectIds);

                $qrCodeDir = public_path('qrcodes');
                if (!File::exists($qrCodeDir)) {
                    File::makeDirectory($qrCodeDir, 0755, true);
                }
                if (!is_writable($qrCodeDir)) {
                    throw new \Exception('Direktori qrcodes tidak memiliki izin tulis.');
                }
                QrCode::format('svg')
                    ->size(400)
                    ->margin(3)
                    ->errorCorrection('H')
                    ->color(40, 40, 40)
                    ->backgroundColor(245, 245, 245)
                    ->generate((string) $barcodeId, public_path('qrcodes/teacher_' . $barcodeId . '.svg'));

                DB::commit();
            }

            if (!empty($errors)) {
                return redirect()->back()->with('errors', $errors);
            }

            return redirect()->back()->with('success', 'Data guru berhasil diimpor.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing teachers: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal mengimpor data guru. Silakan coba lagi atau hubungi admin.');
        }
    }

    public function exportClassroomsTemplate()
    {
        $this->authorize('export_excel');
        $headings = ['Tingkat', 'Jurusan', 'Kode Kelas'];
        $data = [
            ['12', 'RPL', 'A'],
        ];

        return Excel::download(new class ($headings, $data) implements FromArray, WithHeadings {
            protected $headings;
            protected $data;

            public function __construct(array $headings, array $data)
            {
                $this->headings = $headings;
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        }, 'classrooms_template.xlsx');
    }

    public function importClassrooms(Request $request)
    {
        $this->authorize('manage_users');

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ], [
            'file.required' => 'Harap unggah file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat Excel (.xlsx).',
        ]);

        try {
            $rows = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array)
                {
                    return $array;
                }
            }, $request->file('file'))[0];

            $errors = [];
            foreach (array_slice($rows, 1) as $index => $row) {
                if (count($row) < 3) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Mohon lengkapi semua kolom, termasuk Tingkat, Jurusan, dan Kode Kelas.";
                    continue;
                }

                $level = trim($row[0] ?? '');
                $major = trim($row[1] ?? '');
                $classCode = trim($row[2] ?? '');

                if (empty($level) || empty($major) || empty($classCode)) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Ada kolom yang kosong. Pastikan Tingkat, Jurusan, dan Kode Kelas diisi.";
                    continue;
                }

                if (Classroom::where('level', $level)->where('major', $major)->where('class_code', $classCode)->exists()) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Kelas '$level $major $classCode' sudah terdaftar. Gunakan kombinasi lain.";
                    continue;
                }

                Classroom::create([
                    'level' => $level,
                    'major' => $major,
                    'class_code' => $classCode,
                ]);
            }

            if (!empty($errors)) {
                return redirect()->back()->with('errors', $errors);
            }

            return redirect()->back()->with('success', 'Data kelas berhasil diimpor.');
        } catch (\Exception $e) {
            Log::error('Error importing classrooms: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal mengimpor data kelas. Silakan coba lagi atau hubungi admin.');
        }
    }

    public function exportSubjectsTemplate()
    {
        $this->authorize('export_excel');
        $headings = ['Nama Mata Pelajaran'];
        $data = [
            ['Matematika'],
        ];

        return Excel::download(new class ($headings, $data) implements FromArray, WithHeadings {
            protected $headings;
            protected $data;

            public function __construct(array $headings, array $data)
            {
                $this->headings = $headings;
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        }, 'subjects_template.xlsx');
    }

    public function importSubjects(Request $request)
    {
        $this->authorize('manage_users');

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ], [
            'file.required' => 'Harap unggah file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat Excel (.xlsx).',
        ]);

        try {
            $rows = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array)
                {
                    return $array;
                }
            }, $request->file('file'))[0];

            $errors = [];
            foreach (array_slice($rows, 1) as $index => $row) {
                if (count($row) < 1) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Mohon isi kolom Nama Mata Pelajaran.";
                    continue;
                }

                $name = trim($row[0] ?? '');

                if (empty($name)) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Kolom Nama Mata Pelajaran kosong. Harap isi dengan nama mata pelajaran.";
                    continue;
                }

                if (Subject::where('name', $name)->exists()) {
                    $errors[] = "Baris ke-" . ($index + 2) . ": Mata pelajaran '$name' sudah ada.";
                    continue;
                }

                Subject::create([
                    'name' => $name,
                ]);
            }

            if (!empty($errors)) {
                return redirect()->back()->with('errors', $errors);
            }

            return redirect()->back()->with('success', 'Data mata pelajaran berhasil diimpor.');
        } catch (\Exception $e) {
            Log::error('Error importing subjects: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal mengimpor data mata pelajaran. Silakan coba lagi atau hubungi admin.');
        }
    }

    public function exportStudentsExcel()
    {
        $this->authorize('export_excel');
        return Excel::download(new StudentsExport, 'siswa_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportStudentsPdf()
    {
        $this->authorize('export_pdf');
        $students = Student::with('classroom')->get();
        $pdf = Pdf::loadView('admin.exports.students_pdf', compact('students'));
        return $pdf->download('siswa_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportTeachersExcel()
    {
        $this->authorize('export_excel');
        return Excel::download(new TeachersExport, 'guru_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportTeachersPdf()
    {
        $this->authorize('export_pdf');
        $teachers = Teacher::with('subjects')->get();
        $pdf = Pdf::loadView('admin.exports.teachers_pdf', compact('teachers'));
        return $pdf->download('guru_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportClassroomsExcel()
    {
        $this->authorize('export_excel');
        return Excel::download(new ClassroomsExport, 'kelas_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportClassroomsPdf()
    {
        $this->authorize('export_pdf');
        $classrooms = Classroom::all();
        $pdf = Pdf::loadView('admin.exports.classrooms_pdf', compact('classrooms'));
        return $pdf->download('kelas_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportSubjectsExcel()
    {
        $this->authorize('export_excel');
        return Excel::download(new SubjectsExport, 'mata_pelajaran_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportSubjectsPdf()
    {
        $this->authorize('export_pdf');
        $subjects = Subject::all();
        $pdf = Pdf::loadView('admin.exports.subjects_pdf', compact('subjects'));
        return $pdf->download('mata_pelajaran_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportAttendanceExcel(Request $request)
    {

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $type = $request->input('type', 'all');

        $validDates = [];
        $currentDate = Carbon::parse($startDate);
        while ($currentDate->lte(Carbon::parse($endDate))) {
            try {
                if (!$this->holidayService->isHoliday($currentDate)) {
                    $validDates[] = $currentDate->toDateString();
                }
                $currentDate = $this->holidayService->getNextNonHoliday($currentDate);
            } catch (\Exception $e) {
                Log::error('HolidayService error in exportAttendanceExcel: ' . $e->getMessage(), [
                    'date' => $currentDate->toDateString(),
                    'trace' => $e->getTraceAsString(),
                ]);
                if (!$currentDate->isWeekend()) {
                    $validDates[] = $currentDate->toDateString();
                }
                $currentDate->addDay();
            }
        }

        if (empty($validDates)) {
            return redirect()->back()->with('error', 'Tidak ada hari sekolah dalam rentang tanggal yang dipilih.');
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
            ->whereIn(DB::raw('DATE(student_attendances.tanggal)'), $validDates);

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
                ->whereIn(DB::raw('DATE(teacher_attendances.tanggal)'), $validDates);

            if ($type === 'all') {
                $query = $query->union($teacherQuery);
            } elseif ($type === 'teacher') {
                $query = $teacherQuery;
            }
        }

        $attendances = $query->orderBy('tanggal', 'asc')->get();

        return Excel::download(new AttendanceExport($attendances), 'absensi_' . Carbon::now()->format('Ymd') . '.xlsx');
    }

    public function exportAttendancePdf(Request $request)
    {

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $type = $request->input('type', 'all');

        $validDates = [];
        $currentDate = Carbon::parse($startDate);
        while ($currentDate->lte(Carbon::parse($endDate))) {
            try {
                if (!$this->holidayService->isHoliday($currentDate)) {
                    $validDates[] = $currentDate->toDateString();
                }
                $currentDate = $this->holidayService->getNextNonHoliday($currentDate);
            } catch (\Exception $e) {
                Log::error('HolidayService error in exportAttendancePDF: ' . $e->getMessage(), [
                    'date' => $currentDate->toDateString(),
                    'trace' => $e->getTraceAsString(),
                ]);
                if (!$currentDate->isWeekend()) {
                    $validDates[] = $currentDate->toDateString();
                }
                $currentDate->addDay();
            }
        }

        if (empty($validDates)) {
            return redirect()->back()->with('error', 'Tidak ada hari sekolah dalam rentang tanggal yang dipilih.');
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
            ->whereIn(DB::raw('DATE(student_attendances.tanggal)'), $validDates);

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
                ->whereIn(DB::raw('DATE(teacher_attendances.tanggal)'), $validDates);

            if ($type === 'all') {
                $query = $query->union($teacherQuery);
            } elseif ($type === 'teacher') {
                $query = $teacherQuery;
            }
        }

        $attendances = $query->orderBy('tanggal', 'asc')->get();

        $pdf = Pdf::loadView('admin.exports.attendance_pdf', compact('attendances', 'startDate', 'endDate', 'type'));
        return $pdf->download('absensi_' . Carbon::now()->format('Ymd') . '.pdf');
    }
}