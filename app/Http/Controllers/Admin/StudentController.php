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
            'nis' => 'required|unique:students',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->nis),
                'role' => 'student', // Kolom role di tabel users (jika masih digunakan)
            ]);

            // Tugaskan role 'student' ke tabel model_has_roles menggunakan Spatie
            $user->assignRole('student');

            $barcodeId = rand(100000, 999999);
            $student = Student::create([
                'nis' => $request->nis,
                'name' => $request->name,
                'barcode' => $barcodeId,
                'classroom_id' => $request->classroom_id,
                'user_id' => $user->id,
            ]);

            // Ensure qrcodes directory exists
            if (!File::exists(public_path('qrcodes'))) {
                File::makeDirectory(public_path('qrcodes'), 0755, true);
            }

            // Generate QR Code for student
            QrCode::format('svg')
                  ->size(400)
                  ->margin(3)
                  ->errorCorrection('H')
                  ->color(0, 75, 150)
                  ->backgroundColor(245, 245, 245)
                  ->generate((string)$barcodeId, public_path('qrcodes/student_'.$barcodeId.'.svg'));

            return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating student: '.$e->getMessage());
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'classroom_id' => 'required|exists:classrooms,id',
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
                'role' => 'student', // Update kolom role di tabel users (jika masih digunakan)
            ]);

            // Pastikan role 'student' tetap ada di tabel model_has_roles
            $student->user->syncRoles(['student']);

            // Regenerate QR Code if needed
            if (!File::exists(public_path('qrcodes/student_'.$student->barcode.'.svg'))) {
                QrCode::format('svg')
                      ->size(400)
                      ->margin(3)
                      ->errorCorrection('H')
                      ->color(0, 75, 150)
                      ->backgroundColor(245, 245, 245)
                      ->generate((string)$student->barcode, public_path('qrcodes/student_'.$student->barcode.'.svg'));
            }

            return redirect()->route('students.index')->with('success', 'Siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating student: '.$e->getMessage());
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
                    Log::error('Error deleting QR code: '.$e->getMessage());
                }
            }

            if ($student->user) {
                // Hapus semua role dari user di tabel model_has_roles
                $student->user->syncRoles([]);
                $student->user->delete();
            }
            $student->delete();

            return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting student: '.$e->getMessage());
            return back()->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }
}