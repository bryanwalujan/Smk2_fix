<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal - {{ $classroom->full_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6 !important;
        }
        .bg-white {
            background-color: #ffffff !important;
        }
        .text-gray-800 {
            color: #1f2937 !important;
        }
        .text-gray-600 {
            color: #4b5563 !important;
        }
        .text-red-600 {
            color: #dc2626 !important;
        }
        .bg-blue-600 {
            background-color: #2563eb !important;
        }
        .hover\:bg-blue-700:hover {
            background-color: #1d4ed8 !important;
        }
        .bg-gray-600 {
            background-color: #4b5563 !important;
        }
        .hover\:bg-gray-700:hover {
            background-color: #374151 !important;
        }
        .border-gray-200 {
            border-color: #e5e7eb !important;
        }
        .form-input {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
            width: 100%;
        }
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .btn-submit {
            padding: 0.75rem 1.5rem;
            background-color: #2563eb;
            color: #ffffff;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-submit:hover {
            background-color: #1d4ed8;
        }
        .btn-cancel {
            padding: 0.75rem 1.5rem;
            background-color: #6b7280;
            color: #ffffff;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-cancel:hover {
            background-color: #4b5563;
        }
        .note {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.5rem;
            font-style: italic;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Jadwal - {{ $classroom->full_name }}</h1>
                <p class="text-gray-600">Perbarui detail jadwal kelas</p>
            </div>
            <a href="{{ route('admin.schedules.index') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
               aria-label="Kembali ke daftar jadwal">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form id="editScheduleForm" action="{{ route('admin.schedules.update', [$classroom, $schedule]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mata Pelajaran -->
                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-600">Mata Pelajaran</label>
                        <select name="subject_id" id="subject_id" class="form-input" required aria-label="Pilih mata pelajaran">
                            @foreach ($subjects as $id => $name)
                                <option value="{{ $id }}" {{ $schedule->subject_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Guru -->
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-600">Guru</label>
                        <select name="teacher_id" id="teacher_id" class="form-input" required aria-label="Pilih guru">
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $schedule->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hari -->
                    <div>
                        <label for="day" class="block text-sm font-medium text-gray-600">Hari</label>
                        <select name="day" id="day" class="form-input" required aria-label="Pilih hari">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                <option value="{{ $day }}" {{ $schedule->day == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('day')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Waktu Mulai -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-600">Waktu Mulai</label>
                        <input type="time" name="start_time" id="start_time" 
                               value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" 
                               class="form-input" required aria-label="Masukkan waktu mulai">
                        @error('start_time')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Waktu Selesai -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-600">Waktu Selesai</label>
                        <input type="time" name="end_time" id="end_time" 
                               value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" 
                               class="form-input" required aria-label="Masukkan waktu selesai">
                        @error('end_time')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <p class="note mt-4">Catatan: Mengubah hari atau waktu akan menghapus semua pertemuan yang ada dan membuat ulang pertemuan berulang sesuai perubahan.</p>

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="btn-submit" aria-label="Simpan perubahan jadwal">Simpan Perubahan</button>
                    <a href="{{ route('admin.schedules.index') }}" class="btn-cancel" aria-label="Batalkan perubahan">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Notifikasi sukses atau error
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2563eb',
                    background: '#f9fafb',
                    customClass: {
                        popup: 'rounded-xl shadow-lg',
                        title: 'text-2xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626',
                    background: '#f9fafb',
                    customClass: {
                        popup: 'rounded-xl shadow-lg',
                        title: 'text-2xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            // Konfirmasi sebelum submit
            document.getElementById('editScheduleForm').addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Perubahan pada jadwal akan menghapus semua pertemuan yang ada dan membuat ulang pertemuan berulang. Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal',
                    background: '#f9fafb',
                    customClass: {
                        popup: 'rounded-xl shadow-lg',
                        title: 'text-2xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition',
                        cancelButton: 'px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>