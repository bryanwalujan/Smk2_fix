<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sesi Kelas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('layouts.appguru')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Detail Sesi Kelas</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Session Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Informasi Sesi</h2>
            <p><strong>Kelas:</strong> {{ $classSession->classroom->full_name }}</p>
            <p><strong>Mata Pelajaran:</strong> {{ $classSession->subject_name }}</p>
            <p><strong>Hari:</strong> {{ $classSession->day_of_week }}</p>
            <p><strong>Waktu:</strong> {{ $classSession->start_time }} - {{ $classSession->end_time }}</p>
        </div>

        <!-- Materials -->
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Materi</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="p-4 flex justify-between items-center">
                <h3 class="text-lg font-medium">Daftar Materi</h3>
                <a href="{{ route('teacher.lms.create_material', $classSession) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i> Tambah Materi
                </a>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($classSession->materials as $material)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $material->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($material->file_path)
                                    <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-file"></i> Lihat File
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('teacher.lms.show_material', [$classSession, $material]) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-2">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                                <a href="{{ route('teacher.lms.edit_material', [$classSession, $material]) }}"
                                   class="text-yellow-600 hover:text-yellow-900 mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('teacher.lms.destroy_material', [$classSession, $material]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Yakin ingin menghapus materi ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada materi untuk sesi ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Assignments -->
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Tugas</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 flex justify-between items-center">
                <h3 class="text-lg font-medium">Daftar Tugas</h3>
                <a href="{{ route('teacher.lms.create_assignment', $classSession) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i> Tambah Tugas
                </a>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenggat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($classSession->assignments as $assignment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $assignment->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('teacher.lms.show_assignment', [$classSession, $assignment]) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-2">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                                <a href="{{ route('teacher.lms.edit_assignment', [$classSession, $assignment]) }}"
                                   class="text-yellow-600 hover:text-yellow-900 mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('teacher.lms.show_submissions', $assignment) }}"
                                   class="text-green-600 hover:text-green-900 mr-2">
                                    <i class="fas fa-tasks"></i> Pengumpulan
                                </a>
                                <form action="{{ route('teacher.lms.destroy_assignment', [$classSession, $assignment]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Yakin ingin menghapus tugas ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada tugas untuk sesi ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>