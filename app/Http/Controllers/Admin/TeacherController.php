<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user', 'classroom', 'subjects')->get();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        return view('admin.teachers.create', compact('classrooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:teachers,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
            'classroom' => 'nullable|exists:classrooms,id',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'subject_ids.required' => 'Pilih setidaknya satu mata pelajaran.',
            'subject_ids.*.exists' => 'Mata pelajaran yang dipilih tidak valid.',
            'classroom.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            // Buat user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->nip),
                'role' => 'teacher',
            ]);

            // Generate barcode
            $barcodeId = rand(100000, 999999);

            // Buat teacher
            $teacher = Teacher::create([
                'nip' => $request->nip,
                'name' => $request->name,
                'barcode' => $barcodeId,
                'user_id' => $user->id,
                'classroom_id' => $request->classroom ?: null,
            ]);

            // Simpan relasi mata pelajaran
            $teacher->subjects()->sync($request->subject_ids);

            // Pastikan direktori qrcodes ada dan memiliki izin tulis
            $qrCodeDir = public_path('qrcodes');
            if (!File::exists($qrCodeDir)) {
                File::makeDirectory($qrCodeDir, 0755, true);
            }
            if (!is_writable($qrCodeDir)) {
                throw new \Exception('Direktori qrcodes tidak memiliki izin tulis.');
            }

            // Generate QR Code
            QrCode::format('svg')
                  ->size(400)
                  ->margin(3)
                  ->errorCorrection('H')
                  ->color(40, 40, 40)
                  ->backgroundColor(245, 245, 245)
                  ->generate((string)$barcodeId, public_path('qrcodes/teacher_' . $barcodeId . '.svg'));

            DB::commit();
            return redirect()->route('teachers.index')->with('success', 'Guru berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating teacher: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Gagal menambahkan guru: ' . $e->getMessage());
        }
    }

    public function edit(Teacher $teacher)
    {
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        $selectedClassroom = $teacher->classroom_id;
        $selectedSubjectIds = $teacher->subjects->pluck('id')->toArray();
        
        return view('admin.teachers.edit', compact(
            'teacher', 
            'classrooms', 
            'subjects',
            'selectedClassroom',
            'selectedSubjectIds'
        ));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'nip' => 'required|unique:teachers,nip,' . $teacher->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
            'classroom' => 'nullable|exists:classrooms,id',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'subject_ids.required' => 'Pilih setidaknya satu mata pelajaran.',
            'subject_ids.*.exists' => 'Mata pelajaran yang dipilih tidak valid.',
            'classroom.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            // Update teacher
            $teacher->update([
                'nip' => $request->nip,
                'name' => $request->name,
                'classroom_id' => $request->classroom ?: null,
            ]);

            // Update user
            $teacher->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update relasi mata pelajaran
            $teacher->subjects()->sync($request->subject_ids);

            // Regenerate QR Code jika barcode berubah atau file hilang
            $qrCodePath = public_path('qrcodes/teacher_' . $teacher->barcode . '.svg');
            if (!File::exists($qrCodePath)) {
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
                      ->generate((string)$teacher->barcode, $qrCodePath);
            }

            DB::commit();
            return redirect()->route('teachers.index')->with('success', 'Guru berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating teacher: ' . $e->getMessage(), [
                'request' => $request->all(),
                'teacher_id' => $teacher->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Gagal memperbarui guru: ' . $e->getMessage());
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            DB::beginTransaction();

            // Delete QR code file
            $qrCodePath = public_path('qrcodes/teacher_' . $teacher->barcode . '.svg');
            if (File::exists($qrCodePath)) {
                File::delete($qrCodePath);
            }

            // Delete user and teacher
            if ($teacher->user) {
                $teacher->user->delete();
            }
            $teacher->delete();

            DB::commit();
            return redirect()->route('teachers.index')->with('success', 'Guru berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting teacher: ' . $e->getMessage(), [
                'teacher_id' => $teacher->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Gagal menghapus guru: ' . $e->getMessage());
        }
    }

    public function generateQRCodeImage($barcode)
    {
        $qrCodePath = public_path('qrcodes/teacher_' . $barcode . '.svg');
        if (!File::exists($qrCodePath)) {
            return response()->json(['error' => 'QR Code tidak ditemukan'], 404);
        }
        return response()->file($qrCodePath);
    }
}