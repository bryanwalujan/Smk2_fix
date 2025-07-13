<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jadwal Kelas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .btn-primary {
            @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
        }
        .btn-secondary {
            @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
        }
        .btn-icon {
            @apply inline-flex items-center justify-center p-2 rounded-full text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200;
        }
        .search-container {
            @apply relative mb-6;
        }
        .search-input {
            @apply w-full max-w-md pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white transition-all duration-200;
        }
        .search-input:focus {
            @apply transform scale-100 ring-4 ring-indigo-100;
        }
        .search-icon {
            @apply absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500;
        }
        .no-results {
            @apply hidden text-center py-4 text-gray-500 italic;
        }
        .empty-state {
            @apply flex flex-col items-center justify-center py-10 text-center;
        }
    </style>
</head>
<body class="min-h-screen font-sans bg-gray-50">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-full">
                        <i class="fas fa-calendar-alt text-indigo-700 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Daftar Jadwal Kelas</h1>
                        <p class="text-gray-600 text-sm mt-1">Kelola jadwal kelas dan pertemuan secara efisien</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Action Bar -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div class="relative flex-grow max-w-2xl">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
        <input 
            type="text" 
            id="searchInput" 
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
            placeholder="Cari Kelas, Mata Pelajaran, Guru, Hari, atau Waktu..."
        >
    </div>
    <div class="flex gap-3 w-full md:w-auto">
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <a href="{{ route('classrooms.index') }}" class="btn-primary flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Jadwal
        </a>
    </div>
</div>

        <!-- Table Section -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full" id="scheduleTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="scheduleTableBody">
                        @forelse ($schedules as $schedule)
                            <tr class="hover:bg-gray-50 transition-all duration-200">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="font-medium text-gray-800">{{ $schedule->classroom->full_name }}</span>
                                </td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->subject->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->teacher->user->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->day }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.schedules.sessions', $schedule) }}"
                                       class="btn-icon text-indigo-600 hover:text-indigo-700"
                                       title="Lihat Pertemuan">
                                        <i class="fas fa-calendar-check"></i>
                                    </a>
                                    <a href="{{ route('admin.schedules.edit', [$schedule->classroom, $schedule]) }}"
                                       class="btn-icon text-blue-600 hover:text-blue-700"
                                       title="Edit Jadwal">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', [$schedule->classroom, $schedule]) }}"
                                          method="POST" class="inline-block"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini? Semua pertemuan terkait juga akan dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-red-600 hover:text-red-700"
                                                title="Hapus Jadwal">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="6" class="empty-state">
                                    <i class="far fa-calendar-times text-5xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg">Tidak ada data jadwal tersedia.</p>
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
            // SweetAlert2 untuk notifikasi
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
                        confirmButton: 'px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200'
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
                        confirmButton: 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200'
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
                const searchValue = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#scheduleTableBody tr:not(#emptyRow)');
                const noResults = document.getElementById('noResults');
                const emptyRow = document.getElementById('emptyRow');
                let hasVisibleRows = false;

                if (emptyRow) {
                    emptyRow.style.display = searchValue ? 'none' : 'table-row';
                }

                rows.forEach(row => {
                    const classroom = row.cells[0].textContent.toLowerCase();
                    const subject = row.cells[1].textContent.toLowerCase();
                    const teacher = row.cells[2].textContent.toLowerCase();
                    const day = row.cells[3].textContent.toLowerCase();
                    const time = row.cells[4].textContent.toLowerCase();

                    if (
                        classroom.includes(searchValue) ||
                        subject.includes(searchValue) ||
                        teacher.includes(searchValue) ||
                        day.includes(searchValue) ||
                        time.includes(searchValue)
                    ) {
                        row.style.display = 'table-row';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noResults.style.display = hasVisibleRows || !searchValue ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>