<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
        .text-gray-500 {
            color: #6b7280 !important;
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
        .card-shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        }
        .feature-card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }
        .btn-primary {
            padding: 0.75rem 1.5rem;
            background-color: #2563eb;
            color: #ffffff;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .bg-yellow-100 {
            background-color: #fefcbf !important;
        }
        .text-yellow-800 {
            color: #92400e !important;
        }
    </style>
</head>
<body class="h-full flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="bg-blue-600 p-3 rounded-lg mr-3">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Sistem LMS Sekolah</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="bg-gray-200 rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-user text-gray-600"></i>
                    </div>
                    <span class="ml-2 font-medium text-gray-700">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Section -->
            <div class="bg-white rounded-xl p-6 card-shadow mb-8 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="mt-2 text-gray-600">Dashboard Siswa - Sistem LMS Sekolah</p>
                        <div class="mt-2 flex items-center text-gray-600">
                            <i class="fas fa-calendar-day mr-2"></i>
                            <span>Selasa, 1 Juli 2025</span>
                        </div>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Attendance Section -->
            <div class="bg-yellow-100 rounded-xl p-6 card-shadow mb-8 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-yellow-800">Lakukan Absensi untuk Hari Ini!</h2>
                        <p class="mt-2 text-gray-600">Pastikan Anda melakukan absensi untuk hari ini sebelum waktu habis.</p>
                    </div>
                    <a href="{{ route('student.scan') }}" 
                       class="btn-primary inline-flex items-center justify-center"
                       aria-label="Lakukan absensi hari ini">
                        <i class="fas fa-qrcode mr-2"></i> Scan Absensi
                    </a>
                </div>
            </div>

            <!-- LMS Button -->
            <div class="bg-white rounded-xl p-6 card-shadow text-center">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Akses Sistem LMS</h2>
                <p class="text-gray-600 mb-6">Masuk ke Learning Management System untuk mengelola pembelajaran Anda</p>
                <a href="{{ route('student.lms.index') }}" 
                   class="btn-primary inline-flex items-center justify-center">
                    <i class="fas fa-door-open mr-2"></i> Masuk ke LMS Sekarang
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
            <div class="flex items-center justify-center">
                <div class="bg-blue-600 p-2 rounded mr-2">
                    <i class="fas fa-graduation-cap text-white"></i>
                </div>
                <span class="text-gray-700 font-medium">Sistem LMS Sekolah</span>
            </div>
            <div class="mt-4 text-gray-500 text-sm">
                © 2025 Sistem LMS Sekolah. Hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>
</body>
</html>