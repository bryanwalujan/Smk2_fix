<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Jadwal Kelas</title>
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
                <h1 class="text-2xl font-bold text-gray-800">Lihat Jadwal Kelas</h1>
                <p class="text-gray-600">Daftar seluruh jadwal yang terdaftar dalam sistem</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
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
                            <tr>
                                <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                    Tidak ada data jadwal tersedia.
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