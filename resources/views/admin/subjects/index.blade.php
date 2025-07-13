<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mata Pelajaran</title>
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
        .text-gray-500 {
            color: #6b7280 !important;
        }
        .text-gray-900 {
            color: #111827 !important;
        }
        .text-indigo-600 {
            color: #4f46e5 !important;
        }
        .hover\:text-indigo-900:hover {
            color: #3730a3 !important;
        }
        .text-red-600 {
            color: #dc2626 !important;
        }
        .hover\:text-red-900:hover {
            color: #7f1d1d !important;
        }
        .bg-red-600 {
            background-color: #dc2626 !important;
        }
        .hover\:bg-red-700:hover {
            background-color: #b91c1c !important;
        }
        .bg-green-100 {
            background-color: #d1fae5 !important;
        }
        .border-green-400 {
            border-color: #34d399 !important;
        }
        .text-green-700 {
            color: #047857 !important;
        }
        .divide-gray-200 {
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
<body class="bg-gray-100">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Mata Pelajaran</h1>
            <a href="{{ route('subjects.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                <i class="fas fa-plus mr-2"></i> Tambah Mata Pelajaran
            </a>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari berdasarkan Nama Mata Pelajaran...">
        </div>

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
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
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
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
                });
            </script>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            @if ($subjects->isEmpty())
                <p id="emptyMessage" class="text-gray-500">Belum ada mata pelajaran.</p>
                <div id="noResults" class="no-results">Tidak ada data yang cocok dengan pencarian Anda.</div>
            @else
                <table class="min-w-full divide-y divide-gray-200" id="subjectTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mata Pelajaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="subjectTableBody">
                        @foreach ($subjects as $index => $subject)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subject->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('subjects.edit', $subject) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="noResults" class="no-results">Tidak ada data yang cocok dengan pencarian Anda.</div>
            @endif
        </div>
    </div>

    <script>
        // SweetAlert2 untuk konfirmasi hapus
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: 'Mata pelajaran ini akan dihapus secara permanen!',
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

            // Fungsi Live Search
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchValue = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#subjectTableBody tr');
                    const noResults = document.getElementById('noResults');
                    const emptyMessage = document.getElementById('emptyMessage');
                    let hasVisibleRows = false;

                    // Sembunyikan pesan "Belum ada mata pelajaran" jika ada input pencarian
                    if (emptyMessage) {
                        emptyMessage.style.display = searchValue ? 'none' : '';
                    }

                    if (rows.length > 0) {
                        rows.forEach(row => {
                            const subjectName = row.cells[1].textContent.toLowerCase();

                            if (subjectName.includes(searchValue)) {
                                row.style.display = '';
                                hasVisibleRows = true;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        noResults.style.display = hasVisibleRows ? 'none' : 'block';
                    } else {
                        noResults.style.display = searchValue ? 'block' : 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>