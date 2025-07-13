
@extends('layouts.appguru')

@section('title', 'Dashboard LMS Guru')

@section('styles')
    <style>
        /* Warna menggunakan hex untuk kompatibilitas dengan html2canvas */
        body {
            background-color: #f3f4f6 !important;
            font-family: 'Poppins', sans-serif;
            color: #1f2937;
        }

        .bg-white {
            background-color: #ffffff !important;
        }

        .bg-gray-50 {
            background-color: #f9fafb !important;
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

        .text-blue-600 {
            color: #2563eb !important;
        }

        .hover\:text-blue-900:hover {
            color: #1e40af !important;
        }

        .bg-blue-500 {
            background-color: #3b82f6 !important;
        }

        .hover\:bg-blue-600:hover {
            background-color: #2563eb !important;
        }

        .bg-green-100 {
            background-color: #d1fae5 !important;
        }

        .border-green-500 {
            border-color: #10b981 !important;
        }

        .text-green-700 {
            color: #047857 !important;
        }

        .bg-red-100 {
            background-color: #fee2e2 !important;
        }

        .border-red-500 {
            border-color: #ef4444 !important;
        }

        .text-red-700 {
            color: #b91c1c !important;
        }

        .card {
            background: #ffffff;
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
            background-color: #3b82f6;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-warning {
            background-color: #f7b801;
            color: #ffffff;
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
            background-color: #e5e7eb;
            color: #6b7280;
            font-weight: 600;
        }

        /* CSS khusus untuk QR code, meniru Daftar Guru */
        .qr-container {
            position: relative;
            display: inline-block;
        }

        .qr-download-btn {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.7);
            color: #ffffff;
            text-align: center;
            padding: 2px 0;
            font-size: 12px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s;
            border-bottom-left-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        .qr-container:hover .qr-download-btn {
            opacity: 1;
        }

        .qr-preview {
            width: 100px;
            height: 100px;
            image-rendering: crisp-edges;
            border: 1px solid #e5e7eb;
            border-radius: 0.25rem;
        }

        .qr-download-template {
            position: absolute;
            left: -9999px;
            width: 600px;
            padding: 20px;
            background: #ffffff;
            text-align: center;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .qr-download-image {
            width: 500px;
            height: 500px;
            margin: 0 auto;
            display: block;
            image-rendering: crisp-edges;
        }

        .qr-download-name {
            font-size: 28px;
            font-weight: bold;
            margin-top: 20px;
            color: #2d3748;
            padding: 0 20px;
        }

        .qr-download-footer {
            font-size: 16px;
            color: #718096;
            margin-top: 15px;
        }

        .attendance-alert {
            background: linear-gradient(135deg, #fff9db 0%, #ffec99 100%);
            border-left: 4px solid #f7b801;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #ffffff;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #4895ef 0%, #3b82f6 100%);
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
            max-width: 400px;
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
                max-width: 100%;
            }
        }

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
@endsection

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Welcome Section -->
    <div class="card p-6 mb-8 animate-fadeIn">
        <div class="flex items-center justify-between welcome-section">
            <div class="welcome-content">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Selamat Datang, <span style="color: #3b82f6;">{{ Auth::user()->name }}</span>!</h1>
                <p class="text-gray-600 mb-4">Dashboard Guru - Sistem LMS Sekolah</p>
                <div class="flex items-center justify-center md:justify-start text-gray-600 dark:text-gray-300">
                <div class="flex items-center bg-gray-100 rounded-lg px-3 py-2 shadow-sm">
                    <i class="fas fa-calendar-day mr-2 text-blue-500 dark:text-blue-400"></i>
                    <span id="current-date" class="font-medium text-sm md:text-base">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
            </div>
            <div class="flex items-center space-x-4">
                @php
                    $qrPath = 'qrcodes/teacher_' . Auth::user()->barcode . '.svg';
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

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Mata Pelajaran</p>
                    <h3 class="text-2xl font-bold">{{ $uniqueSubjectsCount }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Jadwal Hari Ini</p>
                    <h3 class="text-2xl font-bold">{{ $classSessions->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-school text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Total Kelas</p>
                    <h3 class="text-2xl font-bold">{{ count($subjectsByClass) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes and Subjects -->
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Kelas dan Mata Pelajaran</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        @if (empty($subjectsByClass))
            <div class="px-6 py-4 text-center text-sm text-gray-500">
                Tidak ada kelas atau mata pelajaran yang tersedia.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach ($subjectsByClass as $className => $subjects)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">{{ $className }}</h3>
                        <ul class="list-disc list-inside text-gray-600">
                            @foreach ($subjects as $subject)
                                <li>{{ $subject }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Today's Schedule -->
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Jadwal Hari Ini</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($classSessions as $session)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if ($session->classroom)
                                {{ $session->classroom->full_name }}
                            @else
                                <span class="text-red-500">Kelas Tidak Ditemukan (ID: {{ $session->classroom_id }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->subject ? $session->subject->name : 'Tidak Ada' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('teacher.lms.show_session', $session->id) }}"
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye mr-1"></i> Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada jadwal untuk hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- All Classes -->
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Semua Kelas</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        @if (empty($subjectsByClass))
            <div class="px-6 py-4 text-center text-sm text-gray-500">
                Tidak ada kelas yang tersedia.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-6">
                @foreach ($subjectsByClass as $className => $subjects)
                    @php
                        // Cari classroom_id berdasarkan full_name dengan normalisasi
                        $classroom = \App\Models\Classroom::whereRaw('LOWER(TRIM(full_name)) = ?', [Str::lower(trim($className))])->first();
                    @endphp
                    @if ($classroom)
                        <a href="{{ route('teacher.lms.class_schedules', $classroom->id) }}"
                           class="block bg-gray-50 rounded-lg p-4 hover:bg-indigo-50 transition-colors duration-200">
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-800">{{ $className }}</h3>
                            </div>
                        </a>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <span class="text-red-500">Kelas tidak ditemukan: {{ $className }} (Periksa data di database)</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Template untuk Download QR Code -->
    <div id="qr-download-template" class="qr-download-template">
        <img id="qr-download-image" class="qr-download-image" src="">
        <div id="qr-download-name" class="qr-download-name"></div>
        <div class="qr-download-footer">Scan QR Code untuk verifikasi</div>
    </div>

    <!-- Skrip JavaScript langsung di content -->
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script>
        // Cek apakah dependensi dimuat
        console.log('html2canvas:', typeof html2canvas);
        console.log('Swal:', typeof Swal);

        // Fungsi downloadQRCode, meniru Daftar Guru dengan notifikasi berhasil
        async function downloadQRCode(teacherId, teacherName) {
            console.log('downloadQRCode dipanggil dengan ID:', teacherId, 'Nama:', teacherName);

            // Dapatkan elemen template
            const template = document.getElementById('qr-download-template');
            const qrImage = document.getElementById('qr-download-image');
            const qrName = document.getElementById('qr-download-name');

            // Set konten
            qrImage.src = document.querySelector(`#qr-container-${teacherId} img`).src;
            qrName.textContent = teacherName.replace('teacher_', '');

            // Tampilkan template sementara
            template.style.left = '0';
            template.style.top = '0';
            template.style.position = 'fixed';
            template.style.zIndex = '10000';

            // Konfigurasi html2canvas untuk kualitas tinggi
            const options = {
                scale: 3,
                logging: true,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                windowWidth: 640,
                windowHeight: 640,
                onclone: (clonedDoc) => {
                    // Hapus semua kelas Tailwind yang mungkin menggunakan warna modern
                    const elements = clonedDoc.querySelectorAll('[class]');
                    elements.forEach(el => {
                        el.classList.forEach(className => {
                            if (className.startsWith('bg-') ||
                                className.startsWith('text-') ||
                                className.startsWith('border-') ||
                                className.startsWith('hover:')) {
                                el.classList.remove(className);
                            }
                        });
                    });

                    // Terapkan gaya inline sederhana
                    const template = clonedDoc.getElementById('qr-download-template');
                    template.style.backgroundColor = '#ffffff';
                    template.style.color = '#2d3748';
                    template.style.width = '600px';
                    template.style.padding = '20px';
                    template.style.textAlign = 'center';
                    template.style.boxShadow = '0 0 5px rgba(0, 0, 0, 0.1)';

                    const imageEl = clonedDoc.getElementById('qr-download-image');
                    imageEl.style.width = '500px';
                    imageEl.style.height = '500px';
                    imageEl.style.margin = '0 auto';
                    imageEl.style.display = 'block';

                    const nameEl = clonedDoc.getElementById('qr-download-name');
                    nameEl.style.color = '#2d3748';
                    nameEl.style.fontSize = '28px';
                    nameEl.style.fontWeight = 'bold';
                    nameEl.style.padding = '0 20px';
                    nameEl.style.marginTop = '20px';

                    const footerEl = clonedDoc.querySelector('.qr-download-footer');
                    footerEl.style.color = '#718096';
                    footerEl.style.fontSize = '16px';
                    footerEl.style.marginTop = '15px';
                }
            };

            try {
                // Tunggu untuk memastikan gambar terload
                await new Promise(resolve => {
                    if (qrImage.complete) {
                        resolve();
                    } else {
                        qrImage.onload = resolve;
                        qrImage.onerror = resolve;
                        setTimeout(resolve, 500);
                    }
                });

                // Konversi ke canvas
                const canvas = await html2canvas(template, options);

                // Kembalikan posisi template
                template.style.left = '-9999px';
                template.style.position = 'absolute';
                template.style.zIndex = '';

                // Download gambar
                const link = document.createElement('a');
                link.download = `QR_${teacherName.replace(/[^a-zA-Z0-9]/g, '_')}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();

                // Tampilkan notifikasi berhasil
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `QR Code untuk ${teacherName.replace('teacher_', '')} berhasil di-download.`,
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });

            } catch (error) {
                console.error('Error menghasilkan QR Code:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menghasilkan QR Code. Silakan coba lagi.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Set tanggal saat ini
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);

            // Notifikasi Sukses/Gagal
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            @endif

            // Cek apakah fungsi downloadQRCode tersedia
            console.log('downloadQRCode tersedia:', typeof downloadQRCode);
        });
    </script>
</div>
@endsection

@section('scripts')
    <script>
        // Cek ulang apakah fungsi downloadQRCode tersedia di section scripts
        console.log('downloadQRCode di section scripts:', typeof downloadQRCode);
    </script>
@endsection
