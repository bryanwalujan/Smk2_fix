<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas</title>
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
        .text-red-600 {
            color: #dc2626 !important;
        }
        .hover\:bg-gray-100:hover {
            background-color: #f3f4f6 !important;
        }
        .hover\:bg-blue-50:hover {
            background-color: #eff6ff !important;
        }
        .hover\:bg-green-50:hover {
            background-color: #f0fdf4 !important;
        }
        .hover\:bg-red-50:hover {
            background-color: #fef2f2 !important;
        }
        .bg-blue-600 {
            background-color: #2563eb !important;
        }
        .hover\:bg-blue-700:hover {
            background-color: #1d4ed8 !important;
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

        /* Style untuk section heading */
        .section-heading {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
            margin: 2rem 0 1rem;
            padding-left: 1rem;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')
    
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Kelas</h1>
                <p class="text-gray-600">Daftar seluruh kelas yang terdaftar dalam sistem</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <a href="{{ route('classrooms.create') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Kelas</span>
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari berdasarkan Kelas, Tingkat, Jurusan, atau Kode...">
        </div>

        <!-- Kelas 10 -->
        <div class="level-section" data-level="10">
            <h2 class="section-heading">Kelas 10</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="classroomTable10">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="classroomTableBody10">
                            @forelse ($classrooms->filter(fn($classroom) => $classroom->level == '10') as $classroom)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <a href="{{ route('classrooms.show', $classroom) }}" 
                                           class="font-medium text-blue-600 hover:underline">
                                            {{ $classroom->full_name }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $classroom->level }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $classroom->major }}</td>
                                    <td class="py-4 px-6 text-gray-600 font-mono">{{ $classroom->class_code }}</td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('classrooms.show', $classroom) }}" 
                                               class="p-2 text-gray-600 hover:bg-gray-50 rounded-full transition"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.schedules.create', $classroom) }}" 
                                               class="p-2 text-green-600 hover:bg-green-50 rounded-full transition"
                                               title="Tambah Jadwal">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                            <a href="{{ route('classrooms.edit', $classroom) }}" 
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                                               title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-full transition"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow10">
                                    <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                        Tidak ada data kelas tersedia untuk Kelas 10.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="noResults10" class="no-results">Tidak ada data kelas di Kelas 10 yang cocok dengan pencarian Anda.</div>
                </div>
            </div>
        </div>

        <!-- Kelas 11 -->
        <div class="level-section" data-level="11">
            <h2 class="section-heading">Kelas 11</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="classroomTable11">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="classroomTableBody11">
                            @forelse ($classrooms->filter(fn($classroom) => $classroom->level == '11') as $classroom)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <a href="{{ route('classrooms.show', $classroom) }}" 
                                           class="font-medium text-blue-600 hover:underline">
                                            {{ $classroom->full_name }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $classroom->level }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $classroom->major }}</td>
                                    <td class="py-4 px-6 text-gray-600 font-mono">{{ $classroom->class_code }}</td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('classrooms.show', $classroom) }}" 
                                               class="p-2 text-gray-600 hover:bg-gray-50 rounded-full transition"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.schedules.create', $classroom) }}" 
                                               class="p-2 text-green-600 hover:bg-green-50 rounded-full transition"
                                               title="Tambah Jadwal">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                            <a href="{{ route('classrooms.edit', $classroom) }}" 
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                                               title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-full transition"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow11">
                                    <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                        Tidak ada data kelas tersedia untuk Kelas 11.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="noResults11" class="no-results">Tidak ada data kelas di Kelas 11 yang cocok dengan pencarian Anda.</div>
                </div>
            </div>
        </div>

        <!-- Kelas 12 -->
        <div class="level-section" data-level="12">
            <h2 class="section-heading">Kelas 12</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="classroomTable12">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="classroomTableBody12">
                            @forelse ($classrooms->filter(fn($classroom) => $classroom->level == '12') as $classroom)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <a href="{{ route('classrooms.show', $classroom) }}" 
                                           class="font-medium text-blue-600 hover:underline">
                                            {{ $classroom->full_name }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $classroom->level }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $classroom->major }}</td>
                                    <td class="py-4 px-6 text-gray-600 font-mono">{{ $classroom->class_code }}</td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('classrooms.show', $classroom) }}" 
                                               class="p-2 text-gray-600 hover:bg-gray-50 rounded-full transition"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.schedules.create', $classroom) }}" 
                                               class="p-2 text-green-600 hover:bg-green-50 rounded-full transition"
                                               title="Tambah Jadwal">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                            <a href="{{ route('classrooms.edit', $classroom) }}" 
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                                               title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-full transition"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow12">
                                    <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                        Tidak ada data kelas tersedia untuk Kelas 12.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="noResults12" class="no-results">Tidak ada data kelas di Kelas 12 yang cocok dengan pencarian Anda.</div>
                </div>
            </div>
        </div>

        <!-- General No Results Message -->
        <div id="noResults" class="no-results">Tidak ada data yang cocok dengan pencarian Anda.</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // SweetAlert2 untuk konfirmasi hapus
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: 'Kelas ini akan dihapus secara permanen!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

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

            // Fungsi Live Search
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchValue = e.target.value.toLowerCase();
                const sections = document.querySelectorAll('.level-section');
                const generalNoResults = document.getElementById('noResults');
                let hasVisibleSections = false;

                sections.forEach(section => {
                    const level = section.getAttribute('data-level');
                    const rows = section.querySelectorAll(`#classroomTableBody${level} tr:not([id^=emptyRow])`);
                    const noResults = section.querySelector(`#noResults${level}`);
                    const emptyRow = section.querySelector(`#emptyRow${level}`);
                    let hasVisibleRows = false;

                    if (emptyRow) {
                        emptyRow.style.display = searchValue ? 'none' : '';
                    }

                    rows.forEach(row => {
                        const fullName = row.cells[0].textContent.toLowerCase();
                        const level = row.cells[1].textContent.toLowerCase();
                        const major = row.cells[2].textContent.toLowerCase();
                        const classCode = row.cells[3].textContent.toLowerCase();

                        if (
                            fullName.includes(searchValue) ||
                            level.includes(searchValue) ||
                            major.includes(searchValue) ||
                            classCode.includes(searchValue)
                        ) {
                            row.style.display = '';
                            hasVisibleRows = true;
                            hasVisibleSections = true;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    noResults.style.display = hasVisibleRows ? 'none' : 'block';
                    section.style.display = hasVisibleRows || !searchValue ? 'block' : 'none';
                });

                generalNoResults.style.display = hasVisibleSections ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>