<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user', 'classrooms')->get();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $classrooms = Classroom::all();
        return view('admin.teachers.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:teachers',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'subjects' => 'required|string',
            'classroom' => 'nullable|exists:classrooms,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->nip),
                'role' => 'teacher',
            ]);

            $barcodeId = rand(100000, 999999);
            $teacher = Teacher::create([
                'nip' => $request->nip,
                'name' => $request->name,
                'barcode' => $barcodeId,
                'user_id' => $user->id,
            ]);

            // Generate QR Code
            if (!File::exists(public_path('qrcodes'))) {
                File::makeDirectory(public_path('qrcodes'), 0755, true);
            }

            QrCode::format('svg')
                  ->size(400)
                  ->margin(3)
                  ->errorCorrection('H')
                  ->color(40, 40, 40)
                  ->backgroundColor(245, 245, 245)
                  ->generate((string)$barcodeId, public_path('qrcodes/teacher_'.$barcodeId.'.svg'));

            // Proses subjects dan classroom hanya jika classroom diisi
            if ($request->filled('classroom')) {
                $subjects = array_map('trim', explode(',', $request->subjects));
                foreach ($subjects as $subject_name) {
                    if (!empty($subject_name)) {
                        $teacher->classrooms()->attach($request->classroom, ['subject_name' => $subject_name]);
                    }
                }
            }

            return redirect()->route('teachers.index')->with('success', 'Guru berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating teacher: '.$e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan guru. Silakan coba lagi.');
        }
    }

    public function edit(Teacher $teacher)
    {
        $classrooms = Classroom::all();
        $selectedClassroom = $teacher->classrooms->first()->id ?? null;
        $selectedSubjects = $teacher->classrooms()->pluck('teacher_classroom_subject.subject_name')->unique()->toArray();
        
        return view('admin.teachers.edit', compact(
            'teacher', 
            'classrooms', 
            'selectedClassroom', 
            'selectedSubjects'
        ));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'nip' => 'required|unique:teachers,nip,' . $teacher->id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'subjects' => 'required|string',
            'classroom' => 'nullable|exists:classrooms,id',
        ]);

        try {
            $teacher->update([
                'nip' => $request->nip,
                'name' => $request->name,
            ]);

            $teacher->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Regenerate QR Code if needed
            if (!File::exists(public_path('qrcodes/teacher_'.$teacher->barcode.'.svg'))) {
                QrCode::format('svg')
                      ->size(400)
                      ->margin(3)
                      ->errorCorrection('H')
                      ->color(40, 40, 40)
                      ->backgroundColor(245, 245, 245)
                      ->generate((string)$teacher->barcode, public_path('qrcodes/teacher_'.$teacher->barcode.'.svg'));
            }

            // Proses subjects dan classroom hanya jika classroom diisi
            if ($request->filled('classroom')) {
                $subjects = array_map('trim', explode(',', $request->subjects));
                // Hapus semua relasi classroom sebelumnya
                $teacher->classrooms()->sync([]);
                foreach ($subjects as $subject_name) {
                    if (!empty($subject_name)) {
                        $teacher->classrooms()->attach($request->classroom, ['subject_name' => $subject_name]);
                    }
                }
            } else {
                // Jika classroom tidak diisi, hapus semua relasi classroom
                $teacher->classrooms()->sync([]);
            }

            return redirect()->route('teachers.index')->with('success', 'Guru berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating teacher: '.$e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui guru. Silakan coba lagi.');
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            // Delete QR code file if exists
            $qrCodePath = public_path('qrcodes/teacher_'.$teacher->barcode.'.svg');
            if (File::exists($qrCodePath)) {
                try {
                    File::delete($qrCodePath);
                } catch (\Exception $e) {
                    Log::error('Error deleting QR code: '.$e->getMessage());
                }
            }

            // Delete user and teacher
            if ($teacher->user) {
                $teacher->user->delete();
            }
            $teacher->delete();

            return redirect()->route('teachers.index')->with('success', 'Guru berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting teacher: '.$e->getMessage());
            return back()->with('error', 'Gagal menghapus guru. Silakan coba lagi.');
        }
    }
}