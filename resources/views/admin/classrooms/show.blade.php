<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kelas - {{ $classroom->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Detail Kelas: {{ $classroom->full_name }}</h1>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kelas</h2>
            <p><strong>Tingkat:</strong> {{ $classroom->level }}</p>
            <p><strong>Jurusan:</strong> {{ $classroom->major }}</p>
            <p><strong>Kode Kelas:</strong> {{ $classroom->class_code }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Jadwal Kelas</h2>
                <a href="{{ route('schedules.create', $classroom) }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Tambah Jadwal</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($classroom->schedules as $schedule)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->subject->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->teacher->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->day }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('schedules.edit', [$classroom, $schedule]) }}" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                                           title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('schedules.destroy', [$classroom, $schedule]) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-full transition"
                                                    title="Hapus"
                                                    onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                    Tidak ada jadwal tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('classrooms.index') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>
</body>
</html>