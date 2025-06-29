<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Jadwal Kelas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .text-blue-600 {
            color: #2563eb !important;
        }
        .hover\:text-blue-700:hover {
            color: #1d4ed8 !important;
        }
        .bg-blue-600 {
            background-color: #2563eb !important;
        }
        .hover\:bg-gray-100:hover {
            background-color: #f3f4f6 !important;
        }
        .border-gray-200 {
            border-color: #e5e7eb !important;
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Lihat Jadwal Kelas</h1>
                <p class="text-gray-600">Daftar seluruh jadwal yang terdaftar dalam sistem</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari berdasarkan Kelas, Mata Pelajaran, Guru, Hari, atau Waktu...">
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="scheduleTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="scheduleTableBody">
                        @forelse ($schedules as $schedule)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="font-medium text-gray-800">{{ $schedule->classroom->full_name }}</span>
                                </td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->subject->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->teacher->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->day }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                    Tidak ada data jadwal tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="noResults" class="no-results">Tidak ada data yang cocok dengan pencarian Anda.</div>
            </div>
        </div>
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
                const rows = document.querySelectorAll('#scheduleTableBody tr:not(#emptyRow)');
                const noResults = document.getElementById('noResults');
                const emptyRow = document.getElementById('emptyRow');
                let hasVisibleRows = false;

                // Sembunyikan pesan "Tidak ada data jadwal tersedia" jika ada input pencarian
                if (emptyRow) {
                    emptyRow.style.display = searchValue ? 'none' : '';
                }

                rows.forEach(row => {
                    const classroom = row.cells[0].textContent.toLowerCase();
                    const subject = row.cells[1].textContent.toLowerCase();
                    const teacher = row.cells[2].textContent.toLowerCase();
                    const day = row.cells[3].textContent.toLowerCase();
                    const time = row.cells[4].textContent.toLowerCase();

                    if (
                        classroom.includes(search pct2
                        subject.includes(searchValue) ||
                        teacher.includes(searchValue) ||
                        day.includes(searchValue) ||
                        time.includes(searchValue)
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