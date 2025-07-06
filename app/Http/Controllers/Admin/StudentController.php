<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('classroom', 'user')->get();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classrooms = Classroom::all();
        return view('admin.students.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:students,nis',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'classroom_id' => 'required|exists:classrooms,id',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'classroom_id.required' => 'Kelas wajib dipilih.',
            'classroom_id.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        try {
            Log::info('Attempting to create student', [
                'nis' => $request->nis,
                'email' => $request->email,
                'classroom_id' => $request->classroom_id,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->nis),
                'role' => 'student',
            ]);

            $user->assignRole('student');

            $barcodeId = rand(100000, 999999);
            $student = Student::create([
                'nis' => $request->nis,
                'name' => $request->name,
                'barcode' => $barcodeId,
                'classroom_id' => $request->classroom_id,
                'user_id' => $user->id,
            ]);

            if (!File::exists(public_path('qrcodes'))) {
                File::makeDirectory(public_path('qrcodes'), 0755, true);
            }

            QrCode::format('svg')
                  ->size(400)
                  ->margin(3)
                  ->errorCorrection('H')
                  ->color(0, 75, 150)
                  ->backgroundColor(245, 245, 245)
                  ->generate((string)$barcodeId, public_path('qrcodes/student_'.$barcodeId.'.svg'));

            Log::info('Student created successfully', [
                'student_id' => $student->id,
                'user_id' => $user->id,
                'barcode' => $barcodeId,
            ]);

            return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating student', [
                'nis' => $request->nis,
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    public function edit(Student $student)
    {
        $classrooms = Classroom::all();
        return view('admin.students.edit', compact('student', 'classrooms'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'classroom_id' => 'required|exists:classrooms,id',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'classroom_id.required' => 'Kelas wajib dipilih.',
            'classroom_id.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        try {
            $student->update([
                'nis' => $request->nis,
                'name' => $request->name,
                'classroom_id' => $request->classroom_id,
            ]);

            $student->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'student',
            ]);

            $student->user->syncRoles(['student']);

            if (!File::exists(public_path('qrcodes/student_'.$student->barcode.'.svg'))) {
                QrCode::format('svg')
                      ->size(400)
                      ->margin(3)
                      ->errorCorrection('H')
                      ->color(0, 75, 150)
                      ->backgroundColor(245, 245, 245)
                      ->generate((string)$student->barcode, public_path('qrcodes/student_'.$student->barcode.'.svg'));
            }

            Log::info('Student updated successfully', [
                'student_id' => $student->id,
                'nis' => $request->nis,
                'email' => $request->email,
            ]);

            return redirect()->route('students.index')->with('success', 'Siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating student', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Gagal memperbarui siswa: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student)
    {
        try {
            $qrCodePath = public_path('qrcodes/student_'.$student->barcode.'.svg');
            if (File::exists($qrCodePath)) {
                try {
                    File::delete($qrCodePath);
                } catch (\Exception $e) {
                    Log::error('Error deleting QR code', [
                        'student_id' => $student->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($student->user) {
                $student->user->syncRoles([]);
                $student->user->delete();
            }
            $student->delete();

            Log::info('Student deleted successfully', ['student_id' => $student->id]);

            return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting student', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }
}