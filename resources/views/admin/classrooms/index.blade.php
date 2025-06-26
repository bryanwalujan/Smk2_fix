<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')
    
    <div class="container mx-auto px-4 py-8">
        <!-- Flash Messages Section -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

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

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($classrooms as $classroom)
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
                                        <a href="{{ route('schedules.create', $classroom) }}" 
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
                                                    title="Hapus"
                                                    onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                    Tidak ada data kelas tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>