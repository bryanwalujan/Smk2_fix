<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --warning: #f7b801;
            --danger: #f72585;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--dark);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: none;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .btn-warning {
            background-color: var(--warning);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            background-color: #e6ac00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(247, 184, 1, 0.3);
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-gray);
            color: var(--gray);
            font-weight: 600;
        }
        
        .qr-container {
            position: relative;
            display: inline-block;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .qr-download-btn {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            text-align: center;
            padding: 8px 0;
            font-size: 12px;
            cursor: pointer;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .qr-container:hover .qr-download-btn {
            opacity: 1;
        }
        
        .qr-preview {
            width: 100px;
            height: 100px;
            display: block;
        }
        
        .attendance-alert {
            background: linear-gradient(135deg, #fff9db 0%, #ffec99 100%);
            border-left: 4px solid var(--warning);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);
        }
        
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
        }
        
        .footer {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .features-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .lms-card {
            max-width: 400px; /* Lebar maksimum kartu */
            width: 100%;
        }

        @media (max-width: 768px) {
            .welcome-section {
                flex-direction: column;
                text-align: center;
            }
            
            .welcome-content {
                margin-bottom: 20px;
            }
            
            .feature-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .lms-card {
                max-width: 100%; /* Lebar penuh pada layar kecil */
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .delay-100 {
            animation-delay: 0.1s;
        }
        
        .delay-200 {
            animation-delay: 0.2s;
        }
        
        .delay-300 {
            animation-delay: 0.3s;
        }
    </style>
</head>
<body class="h-full flex flex-col min-h-screen">
    <!-- Header -->
    <header class="navbar shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="gradient-bg p-3 rounded-xl shadow-md">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800 hidden sm:block">Sistem LMS Sekolah</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="avatar">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span class="font-medium text-gray-700 hidden sm:inline">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Section -->
            <div class="card p-6 mb-8 animate-fadeIn">
                <div class="flex items-center justify-between welcome-section">
                    <div class="welcome-content">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Selamat Datang, <span class="text-primary">{{ Auth::user()->name }}</span>!</h1>
                        <p class="text-gray-600 mb-4">Dashboard Siswa - Sistem LMS Sekolah</p>
                        <div class="flex items-center text-gray-500">
                            <i class="fas fa-calendar-day mr-2"></i>
                            <span id="current-date">Mengambil tanggal...</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-100 p-4 rounded-full shadow-inner">
                            <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
                        </div>
                        @php
                            $qrPath = 'qrcodes/student_' . Auth::user()->barcode . '.svg';
                            $qrExists = file_exists(public_path($qrPath));
                        @endphp
                        @if ($qrExists)
                            <div class="qr-container" id="qr-container-{{ Auth::user()->id }}">
                                <img src="{{ asset($qrPath) }}" alt="QR Code" class="qr-preview">
                                <div class="qr-download-btn" onclick="downloadQRCode({{ Auth::user()->id }}, '{{ Auth::user()->name }}')">
                                    <i class="fas fa-download mr-1"></i> Download HQ
                                </div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">QR Code tidak ditemukan</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attendance Section -->
            <div class="card p-6 mb-8 attendance-alert animate-fadeIn delay-100">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-4 md:mb-0 text-center md:text-left">
                        <h2 class="text-xl font-bold text-yellow-800 mb-2">Lakukan Absensi untuk Hari Ini!</h2>
                        <p class="text-gray-700">Pastikan Anda melakukan absensi untuk hari ini sebelum waktu habis.</p>
                    </div>
                    <a href="{{ route('student.scan') }}" 
                       class="btn-warning inline-flex items-center justify-center px-6 py-3"
                       aria-label="Lakukan absensi hari ini">
                        <i class="fas fa-qrcode mr-2"></i> Scan Absensi
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="features-grid">
                <!-- LMS Card -->
                <div class="card p-6 text-center animate-fadeIn delay-200 lms-card">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Learning Management</h3>
                    <p class="text-gray-600 mb-4">Akses materi pembelajaran, tugas, dan sumber belajar lainnya</p>
                    <a href="{{ route('lms.index') }}" 
                       class="btn-primary inline-flex items-center justify-center w-full">
                        <i class="fas fa-door-open mr-2"></i> Masuk ke LMS
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="card p-4 text-center animate-fadeIn delay-300">
                    <div class="text-gray-500 mb-1"><i class="fas fa-book text-lg"></i></div>
                    <div class="text-2xl font-bold text-primary">{{ $quickStats['subjects_count'] }}</div>
                    <div class="text-sm text-gray-500">Mata Pelajaran</div>
                </div>
                <div class="card p-4 text-center animate-fadeIn delay-300">
                    <div class="text-gray-500 mb-1"><i class="fas fa-tasks text-lg"></i></div>
                    <div class="text-2xl font-bold text-primary">{{ $quickStats['active_assignments'] }}</div>
                    <div class="text-sm text-gray-500">Tugas Aktif</div>
                </div>
                <div class="card p-4 text-center animate-fadeIn delay-300">
                    <div class="text-gray-500 mb-1"><i class="fas fa-check-circle text-lg"></i></div>
                    <div class="text-2xl font-bold text-primary">{{ $quickStats['attendance_percentage'] }}%</div>
                    <div class="text-sm text-gray-500">Kehadiran</div>
                </div>
                <div class="card p-4 text-center animate-fadeIn delay-300">
                    <div class="text-gray-500 mb-1"><i class="fas fa-bell text-lg"></i></div>
                    <div class="text-2xl font-bold text-primary">{{ $quickStats['notifications'] }}</div>
                    <div class="text-sm text-gray-500">Pemberitahuan</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Template untuk Download QR Code -->
    <div id="qr-download-template" style="position: absolute; left: -9999px; width: 600px; padding: 20px; background: #ffffff; text-align: center; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
        <img id="qr-download-image" style="width: 500px; height: 500px; margin: 0 auto; display: block;">
        <div id="qr-download-name" style="font-size: 28px; font-weight: bold; margin-top: 20px; color: #2d3748; padding: 0 20px;"></div>
        <div style="font-size: 16px; color: #718096; margin-top: 15px;">Scan QR Code untuk verifikasi</div>
    </div>

    <!-- Footer -->
    <footer class="footer border-t py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="gradient-bg p-2 rounded-lg mr-2">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                    <span class="text-gray-700 font-medium">Sistem LMS Sekolah</span>
                </div>
                <div class="text-gray-500 text-sm">
                    © 2025 Sistem LMS Sekolah. Hak cipta dilindungi.
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Set current date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
            
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4361ee',
                    background: '#f8f9fa',
                    customClass: {
                        popup: 'rounded-xl shadow-xl',
                        title: 'text-2xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition'
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
                    confirmButtonColor: '#f72585',
                    background: '#f8f9fa',
                    customClass: {
                        popup: 'rounded-xl shadow-xl',
                        title: 'text-2xl font-bold text-gray-800',
                        content: 'text-gray-600',
                        confirmButton: 'px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600 transition'
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

        async function downloadQRCode(studentId, studentName) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menghasilkan QR Code, mohon tunggu.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const template = document.getElementById('qr-download-template');
            const qrImage = document.getElementById('qr-download-image');
            const qrName = document.getElementById('qr-download-name');
            const sourceImage = document.querySelector(`#qr-container-${studentId} img`);

            if (!sourceImage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gambar QR Code tidak ditemukan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f72585'
                });
                return;
            }

            qrImage.src = sourceImage.src;
            qrName.textContent = studentName;

            template.style.left = '0';
            template.style.top = '0';
            template.style.position = 'fixed';
            template.style.zIndex = '10000';

            const options = {
                scale: 3,
                logging: false,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                windowWidth: 640,
                windowHeight: 640,
                foreignObjectRendering: false
            };

            try {
                await new Promise(resolve => {
                    if (qrImage.complete) {
                        resolve();
                    } else {
                        qrImage.onload = resolve;
                        qrImage.onerror = resolve;
                        setTimeout(resolve, 500);
                    }
                });

                const canvas = await html2canvas(template, options);

                template.style.left = '-9999px';
                template.style.position = 'absolute';
                template.style.zIndex = '';

                const link = document.createElement('a');
                link.download = `QR_${studentName.replace(/[^a-zA-Z0-9]/g, '_')}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();

                Swal.close();
            } catch (error) {
                console.error('Error generating QR Code:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menghasilkan QR Code. Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f72585'
                });
            }
        }
    </script>
</body>
</html>