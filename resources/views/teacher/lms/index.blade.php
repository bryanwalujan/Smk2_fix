<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard LMS Guru</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('layouts.appguru')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard LMS Guru</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                        <i class="fas fa-book text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Mata Pelajaran</p>
                        <h3 class="text-2xl font-bold">{{ $uniqueSubjectsCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Jadwal Hari Ini</p>
                        <h3 class="text-2xl font-bold">{{ $classSessions->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Jadwal Hari Ini</h2>
<div class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($classSessions as $session)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->classroom->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->subject ? $session->subject->name : 'Tidak Ada' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('teacher.lms.show_session', $session) }}"
                           class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                        Tidak ada jadwal untuk hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Semua Jadwal -->
<h2 class="text-2xl font-semibold text-gray-700 mb-4">Semua Jadwal</h2>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($allClassSessions as $session)
                <tr class="{{ $session->is_today ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->day_of_week }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->classroom->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->subject ? $session->subject->name : 'Tidak Ada' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('teacher.lms.show_session', $session) }}"
                           class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        Tidak ada jadwal yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>