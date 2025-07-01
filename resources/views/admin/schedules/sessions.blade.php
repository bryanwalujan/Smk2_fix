<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pertemuan - {{ $schedule->classroom->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        
        .swal2-popup {
            border-radius: 1rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            overflow: hidden !important;
            padding: 2.5rem !important;
            border: 1px solid #e5e7eb !important;
        }
        
        .swal2-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            margin-bottom: 1.25rem !important;
        }
        
        .swal2-html-container {
            font-size: 1.05rem !important;
            color: #4b5563 !important;
            line-height: 1.6 !important;
        }
        
        .swal2-icon {
            width: 5rem !important;
            height: 5rem !important;
            margin: 1.5rem auto 1.5rem !important;
        }
        
        .swal2-icon.swal2-success {
            border-color: #34d399 !important;
            color: #34d399 !important;
        }
        
        .swal2-icon.swal2-error {
            border-color: #f87171 !important;
            color: #f87171 !important;
        }
        
        .swal2-success-ring {
            border-color: rgba(52, 211, 153, 0.3) !important;
        }
        
        .swal2-success-line {
            background-color: #10b981 !important;
        }
        
        .swal2-x-mark-line {
            background-color: #ef4444 !important;
        }
        
        .swal2-styled.swal2-confirm {
            background-color: #3b82f6 !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 2rem !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3), 0 2px 4px -1px rgba(59, 130, 246, 0.1) !important;
        }
        
        .swal2-styled.swal2-confirm:hover {
            background-color: #2563eb !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3), 0 4px 6px -2px rgba(59, 130, 246, 0.1) !important;
        }
        
        .swal2-styled.swal2-cancel {
            border-radius: 0.75rem !important;
            padding: 0.75rem 2rem !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
        }
        
        .swal2-timer-progress-bar {
            height: 0.25rem !important;
            border-radius: 0 0 0.25rem 0.25rem !important;
        }
        
        .swal2-success .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #34d399, #10b981) !important;
        }
        
        .swal2-error .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #f87171, #ef4444) !important;
        }
        
        .table-row-hover {
            transition: all 0.2s;
        }
        
        .table-row-hover:hover {
            background-color: #f9fafb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        
        .btn-submit {
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3), 0 2px 4px -1px rgba(37, 99, 235, 0.1);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3), 0 4px 6px -2px rgba(37, 99, 235, 0.1);
        }
        
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-pop {
            animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        @keyframes popIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-pop">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pertemuan - {{ $schedule->classroom->full_name }}</h1>
                <p class="text-gray-600 mt-1">Mata Pelajaran: {{ $schedule->subject->name }} | Guru: {{ $schedule->teacher->name }} | Hari: {{ $schedule->day }} | Waktu: {{ $schedule->start_time }} - {{ $schedule->end_time }}</p>
            </div>
            <a href="{{ route('admin.schedules.index') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Form Ubah Tanggal Pertemuan Pertama -->
        <div class="form-container bg-white p-6 rounded-xl shadow-sm border border-gray-200 card fade-in">
            <form action="{{ route('admin.schedules.update_first_session', $schedule) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                    <div class="w-full sm:w-auto">
                        <label for="first_session_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pertemuan Pertama</label>
                        <input type="date" name="first_session_date" id="first_session_date" 
                               value="{{ $classSessions->first() ? $classSessions->first()->date : '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <button type="submit" class="btn-submit bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg mt-2 sm:mt-0">
                        Ubah Tanggal
                    </button>
                </div>
                @error('first_session_date')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card fade-in mt-6">
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
                        @forelse ($classSessions as $session)
                            <tr class="table-row-hover">
                                <td class="py-4 px-6 whitespace-nowrap text-gray-600 font-medium">Pertemuan {{ $session->meeting_number }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $session->day_of_week }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('admin.schedules.delete_session', [$schedule, $session]) }}" 
                                          method="POST" class="inline-block"
                                          onsubmit="return confirmDelete(event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 transition-colors duration-200"
                                                title="Hapus Pertemuan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 px-6 text-center text-gray-500">
                                    Tidak ada pertemuan untuk jadwal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    background: '#ffffff',
                    color: '#1f2937',
                    confirmButtonColor: '#3b82f6',
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    showClass: {
                        popup: 'animate__animated animate__shakeX animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    background: '#ffffff',
                    color: '#1f2937',
                    confirmButtonColor: '#ef4444',
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
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
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                customClass: {
                    popup: 'rounded-xl',
                    title: 'text-xl font-bold',
                    content: 'text-gray-600',
                    confirmButton: 'px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition',
                    cancelButton: 'px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition'
                },
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>