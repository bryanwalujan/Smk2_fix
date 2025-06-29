<!DOCTYPE html>
<html>
<head>
    <title>Students List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
    <style>
        /* Ganti semua warna dengan format hex */
        body {
            background-color: #f3f4f6 !important;
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
        .text-blue-600 {
            color: #2563eb !important;
        }
        .hover\:text-blue-900:hover {
            color: #1e40af !important;
        }
        .text-red-600 {
            color: #dc2626 !important;
        }
        .hover\:text-red-900:hover {
            color: #7f1d1d !important;
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

        /* Style untuk search bar */
        .search-container {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .search-input {
            width: 100%;
            max-width: 500px;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
        }
        .search-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: scale(1.01);
        }
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }
        .no-results {
            display: none;
            text-align: center;
            padding: 1rem;
            color: #6b7280;
            font-style: italic;
        }

        /* Style custom lainnya tetap sama */
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
            color: white;
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
        }
        .qr-download-template {
            position: absolute;
            left: -9999px;
            width: 600px;
            padding: 20px;
            background: #ffffff;
            text-align: center;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
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
    </style>
</head>
<body class="bg-gray-100">
    @include('layouts.navbar-admin')
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Siswa</h1>
            <a href="{{ route('students.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow transition duration-200">Tambah Siswa</a>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari berdasarkan NIS, Nama, atau Kelas...">
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="studentTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="studentTableBody">
                        @foreach ($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->nis }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $student->classroom ? $student->classroom->level . ' ' . $student->classroom->major . ' ' . $student->classroom->class_code : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if (file_exists(public_path('qrcodes/student_'.$student->barcode.'.svg')))
                                        <div class="qr-container" id="qr-container-{{ $student->id }}">
                                            <img src="{{ asset('qrcodes/student_'.$student->barcode.'.svg') }}" alt="QR Code" class="qr-preview rounded border border-gray-200">
                                            <div class="qr-download-btn" onclick="downloadQRCode({{ $student->id }}, '{{ $student->name }}')">Download HQ</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">QR Code tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('students.edit', $student->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="event.preventDefault(); Swal.fire({
                                                title: 'Yakin ingin menghapus?',
                                                text: 'Data siswa {{ $student->name }} akan dihapus secara permanen!',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal',
                                                reverseButtons: true
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    this.closest('form').submit();
                                                }
                                            });">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="noResults" class="no-results">Tidak ada data yang cocok dengan pencarian Anda.</div>
            </div>
        </div>
    </div>

    <!-- Template untuk download QR Code -->
    <div id="qr-download-template" class="qr-download-template">
        <img id="qr-download-image" class="qr-download-image" src="">
        <div id="qr-download-name" class="qr-download-name"></div>
        <div class="qr-download-footer">Scan QR Code untuk verifikasi</div>
    </div>

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

            // Fungsi Live Search
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchValue = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#studentTableBody tr');
                const noResults = document.getElementById('noResults');
                let hasVisibleRows = false;

                rows.forEach(row => {
                    const nis = row.cells[0].textContent.toLowerCase();
                    const name = row.cells[1].textContent.toLowerCase();
                    const classroom = row.cells[2].textContent.toLowerCase();

                    if (
                        nis.includes(searchValue) ||
                        name.includes(searchValue) ||
                        classroom.includes(searchValue)
                    ) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noResults.style.display = hasVisibleRows ? 'none' : 'block';
            });
        });

        async function downloadQRCode(studentId, studentName) {
            // Dapatkan elemen template
            const template = document.getElementById('qr-download-template');
            const qrImage = document.getElementById('qr-download-image');
            const qrName = document.getElementById('qr-download-name');
            
            // Set konten
            qrImage.src = document.querySelector(`#qr-container-${studentId} img`).src;
            qrName.textContent = studentName;
            
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
                    // Hapus semua class Tailwind yang mungkin menggunakan warna modern
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
                    
                    // Terapkan style inline sederhana
                    const template = clonedDoc.getElementById('qr-download-template');
                    template.style.backgroundColor = '#ffffff';
                    template.style.color = '#2d3748';
                    
                    const nameEl = clonedDoc.getElementById('qr-download-name');
                    nameEl.style.color = '#2d3748';
                    nameEl.style.fontSize = '28px';
                    nameEl.style.fontWeight = 'bold';
                    
                    const footerEl = clonedDoc.querySelector('.qr-download-footer');
                    footerEl.style.color = '#718096';
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
                link.download = `QR_${studentName.replace(/[^a-zA-Z0-9]/g, '_')}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                
            } catch (error) {
                console.error('Error generating QR Code:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menghasilkan QR Code. Silakan coba lagi.',
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
                    }
                });
            }
        }
    </script>
</body>
</html>