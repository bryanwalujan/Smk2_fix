@extends('layouts.appguru')

@section('title', 'Dashboard LMS Guru')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard LMS Guru</h1>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
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
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-school text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Total Kelas</p>
                    <h3 class="text-2xl font-bold">{{ count($subjectsByClass) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes and Subjects -->
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Kelas dan Mata Pelajaran</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        @if (empty($subjectsByClass))
            <div class="px-6 py-4 text-center text-sm text-gray-500">
                Tidak ada kelas atau mata pelajaran yang tersedia.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach ($subjectsByClass as $className => $subjects)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">{{ $className }}</h3>
                        <ul class="list-disc list-inside text-gray-600">
                            @foreach ($subjects as $subject)
                                <li>{{ $subject }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if ($session->classroom)
                                {{ $session->classroom->full_name }}
                            @else
                                <span class="text-red-500">Kelas Tidak Ditemukan (ID: {{ $session->classroom_id }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->subject ? $session->subject->name : 'Tidak Ada' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('teacher.lms.show_session', $session->id) }}"
                               class="text-indigo-600 hover:text-indigo-900">
                                <i class="fas fa-eye mr-1"></i> Lihat
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

    <!-- All Classes -->
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Semua Kelas</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        @if (empty($subjectsByClass))
            <div class="px-6 py-4 text-center text-sm text-gray-500">
                Tidak ada kelas yang tersedia.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-6">
                @foreach ($subjectsByClass as $className => $subjects)
                    @php
                        // Cari classroom_id berdasarkan full_name dengan normalisasi
                        $classroom = \App\Models\Classroom::whereRaw('LOWER(TRIM(full_name)) = ?', [Str::lower(trim($className))])->first();
                    @endphp
                    @if ($classroom)
                        <a href="{{ route('teacher.lms.class_schedules', $classroom->id) }}"
                           class="block bg-gray-50 rounded-lg p-4 hover:bg-indigo-50 transition-colors duration-200">
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-800">{{ $className }}</h3>
                            </div>
                        </a>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <span class="text-red-500">Kelas tidak ditemukan: {{ $className }} (Periksa data di database)</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection