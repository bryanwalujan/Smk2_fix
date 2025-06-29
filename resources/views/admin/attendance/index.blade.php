<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Absensi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
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
        .text-gray-600 {
            color: #4b5563 !important;
        }
        .text-gray-500 {
            color: #6b7280 !important;
        }
        .text-gray-700 {
            color: #374151 !important;
        }
        .text-blue-600 {
            color: #2563eb !important;
        }
        .hover\:text-blue-700:hover {
            color: #1d4ed8 !important;
        }
        .text-green-600 {
            color: #16a34a !important;
        }
        .hover\:text-green-700:hover {
            color: #15803d !important;
        }
        .bg-blue-600 {
            background-color: #2563eb !important;
        }
        .hover\:bg-blue-700:hover {
            background-color: #1d4ed8 !important;
        }
        .bg-green-600 {
            background-color: #16a34a !important;
        }
        .hover\:bg-green-700:hover {
            background-color: #15803d !important;
        }
        .border-gray-200 {
            border-color: #e5e7eb !important;
        }
        .border-gray-300 {
            border-color: #d1d5db !important;
        }
        .focus\:border-blue-500:focus {
            border-color: #3b82f6 !important;
        }
        .focus\:ring-blue-500:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5) !important;
        }
        .hover\:bg-gray-100:hover {
            background-color: #f3f4f6 !important;
        }
        .hover\:bg-blue-50:hover {
            background-color: #eff6ff !important;
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
            height: 2.5rem; /* Menyesuaikan tinggi agar konsisten */
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
            font-size: 1rem; /* Menyesuaikan ukuran ikon */
        }
        .no-results {
            display: none;
            text-align: center;
            padding: 1rem;
            color: #6b7280;
            font-style: italic;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Absensi</h1>
                <p class="text-gray-600">Daftar absensi siswa dan guru</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('attendance.create') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Absensi</span>
                </a>
                <a href="{{ route('attendance.scan') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-qrcode"></i>
                    <span>Scan Barcode</span>
                </a>
            </div>
        </div>

        <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari berdasarkan Nama, Tipe, Tanggal, Waktu, Status, atau Metode...">
            </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
            <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-col md:flex-row gap-4 mb-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" id="date" name="date" value="{{ $date }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipe</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="student" {{ $type === 'student' ? 'selected' : '' }}>Siswa</option>
                        <option value="teacher" {{ $type === 'teacher' ? 'selected' : '' }}>Guru</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Search Bar -->
            

            <div class="overflow-x-auto">
                <table class="w-full" id="attendanceTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Masuk</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pulang</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="attendanceTableBody">
                        @forelse ($attendances as $attendance)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->user_name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->user_type === 'student' ? 'Siswa' : 'Guru' }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ Carbon\Carbon::parse($attendance->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->waktu_masuk }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->waktu_pulang ?? '-' }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ ucfirst($attendance->status) }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ ucfirst($attendance->metode_absen) }}</td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <a href="{{ route('attendance.edit', ['id' => $attendance->id, 'type' => $attendance->user_type]) }}"
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                                       title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="8" class="py-4 px-6 text-center text-gray-500">
                                    Tidak ada data absensi tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="noResults" class="no-results">Tidak ada data yang cocok dengan pencarian Anda.</div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // SweetAlert2 untuk notifikasi session
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

            @if (isset($isHoliday) && $isHoliday)
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: '{{ $holidayMessage }}',
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

            // Fungsi Live Search
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchValue = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#attendanceTableBody tr:not(#emptyRow)');
                const noResults = document.getElementById('noResults');
                const emptyRow = document.getElementById('emptyRow');
                let hasVisibleRows = false;

                // Sembunyikan pesan "Tidak ada data absensi tersedia" jika ada input pencarian
                if (emptyRow) {
                    emptyRow.style.display = searchValue ? 'none' : '';
                }

                rows.forEach(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const type = row.cells[1].textContent.toLowerCase();
                    const date = row.cells[2].textContent.toLowerCase();
                    const checkIn = row.cells[3].textContent.toLowerCase();
                    const checkOut = row.cells[4].textContent.toLowerCase();
                    const status = row.cells[5].textContent.toLowerCase();
                    const method = row.cells[6].textContent.toLowerCase();

                    if (
                        name.includes(searchValue) ||
                        type.includes(searchValue) ||
                        date.includes(searchValue) ||
                        checkIn.includes(searchValue) ||
                        checkOut.includes(searchValue) ||
                        status.includes(searchValue) ||
                        method.includes(searchValue)
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
    </script>
</body>
</html>