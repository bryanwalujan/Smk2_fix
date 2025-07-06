<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pertemuan - {{ $schedule->classroom->full_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }

        .btn-primary {
            @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
        }

        .btn-secondary {
            @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
        }

        .btn-icon {
            @apply inline-flex items-center justify-center p-2 rounded-full text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200;
        }

        .card {
            @apply bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden;
        }

        .card-header {
            @apply p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200;
        }

        .table-row-hover {
            @apply transition-all duration-200 hover:bg-gray-50 hover:shadow-md;
        }

        .empty-state {
            @apply flex flex-col items-center justify-center py-10 text-center;
        }

        .swal2-popup {
            @apply rounded-xl shadow-2xl border border-gray-200 p-6 bg-white;
        }

        .swal2-title {
            @apply text-xl font-bold text-gray-800 mb-4;
        }

        .swal2-html-container {
            @apply text-gray-600 text-base leading-relaxed;
        }

        .swal2-icon {
            @apply w-20 h-20 m-auto mb-4;
        }

        .swal2-success .swal2-timer-progress-bar {
            @apply bg-gradient-to-r from-green-400 to-green-600 h-1 rounded-b;
        }

        .swal2-error .swal2-timer-progress-bar {
            @apply bg-gradient-to-r from-red-400 to-red-600 h-1 rounded-b;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header Section -->
        <div class="card mb-8">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-full">
                        <i class="fas fa-calendar-day text-indigo-700 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Pertemuan - {{ $schedule->classroom->full_name }}</h1>
                        <p class="text-gray-600 mt-1 text-sm">
                            Mata Pelajaran: {{ $schedule->subject->name }} | 
                            Guru: {{ $schedule->teacher->user->name }} | 
                            Hari: {{ $schedule->day }} | 
                            Waktu: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Ubah Tanggal Pertemuan Pertama -->
        <div class="card mb-8">
            <div class="p-6">
                <form action="{{ route('admin.schedules.update_first_session', $schedule) }}" method="POST">
                    @csrf
                    @method('POST') <!-- Sesuaikan dengan metode di controller -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                        <div class="w-full sm:w-auto">
                            <label for="first_session_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pertemuan Pertama</label>
                            <input type="date" name="first_session_date" id="first_session_date" 
                                   value="{{ $classSessions->first() ? $classSessions->first()->date : \Carbon\Carbon::today()->toDateString() }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i> Ubah Tanggal
                        </button>
                    </div>
                    @error('first_session_date')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertemuan</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($classSessions as $index => $session)
                            <tr class="table-row-hover">
                                <td class="py-4 px-6 whitespace-nowrap text-gray-600 font-medium">Pertemuan {{ $index + 1 }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $session->day_of_week }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('admin.schedules.delete_session', [$schedule, $session]) }}" 
                                          method="POST" class="inline-block"
                                          onsubmit="return confirmDelete(event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-red-600 hover:text-red-700"
                                                title="Hapus Pertemuan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="far fa-calendar-times text-5xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg">Tidak ada pertemuan untuk jadwal ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-6 flex justify-end">
            <a href="{{ route('admin.schedules.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2563eb',
                    background: '#ffffff',
                    showClass: { popup: 'animate__animated animate__fadeInDown' },
                    hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626',
                    background: '#ffffff',
                    showClass: { popup: 'animate__animated animate__shakeX' },
                    hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            @endif
        });

        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus pertemuan ini dan semua pertemuan berikutnya?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    content: 'swal2-html-container',
                    confirmButton: 'btn-primary',
                    cancelButton: 'btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>