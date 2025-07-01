@extends('layouts.appstudent')

@section('title', 'Pertemuan {{ $subject->name }}')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Pertemuan {{ $subject->name }}</h1>
            <p class="text-sm md:text-base text-gray-600 mt-1 md:mt-2">Daftar pertemuan untuk mata pelajaran {{ $subject->name }}</p>
            <div class="mt-2 flex items-center text-gray-600">
                <i class="fas fa-calendar-day mr-2"></i>
            </div>
        </div>
        <a href="{{ route('lms.index') }}" 
           class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
           aria-label="Kembali ke daftar mata pelajaran">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Current Week Sessions -->
    <div class="mb-12">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">Pertemuan Minggu Ini</h2>
        @if ($currentWeekSessions->isEmpty())
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm md:text-base">
                <p>Tidak ada pertemuan minggu ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($currentWeekSessions as $session)
                    <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4 shadow-sm hover:shadow-md transition feature-card">
                        <div class="flex items-center mb-3">
                            <div class="bg-yellow-200 p-2 rounded-full mr-3">
                                <i class="fas fa-calendar text-yellow-600 text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $session->subject->name ?? 'Tidak ada mata pelajaran' }}</h4>
                                <p class="text-sm text-gray-600">{{ $session->date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 mb-3">
                            <p><strong>Hari:</strong> {{ ucfirst($session->day_of_week) }}</p>
                            <p><strong>Jam:</strong> {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</p>
                            <p><strong>Guru:</strong> {{ $session->teacher->name ?? 'Tidak ada guru' }}</p>
                        </div>
                        <a href="{{ route('lms.show_session', $session) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                           aria-label="Lihat detail sesi {{ $session->subject->name }}">
                            <i class="fas fa-eye mr-2"></i> Lihat Sesi
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Upcoming Sessions -->
    <div class="mb-12">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">Pertemuan yang Akan Datang</h2>
        @if ($upcomingSessions->isEmpty())
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm md:text-base">
                <p>Tidak ada pertemuan yang akan datang.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($upcomingSessions as $session)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition feature-card">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-100 p-2 rounded-full mr-3">
                                <i class="fas fa-clock text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $session->subject->name ?? 'Tidak ada mata pelajaran' }}</h4>
                                <p class="text-sm text-gray-600">{{ $session->date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 mb-3">
                            <p><strong>Hari:</strong> {{ ucfirst($session->day_of_week) }}</p>
                            <p><strong>Jam:</strong> {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</p>
                            <p><strong>Guru:</strong> {{ $session->teacher->name ?? 'Tidak ada guru' }}</p>
                        </div>
                        <a href="{{ route('lms.show_session', $session) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                           aria-label="Lihat detail sesi {{ $session->subject->name }}">
                            <i class="fas fa-eye mr-2"></i> Lihat Sesi
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Past Sessions -->
    <div class="mb-12">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">Pertemuan yang Sudah Lewat</h2>
        @if ($pastSessions->isEmpty())
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm md:text-base">
                <p>Tidak ada pertemuan yang sudah lewat.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($pastSessions as $session)
                    <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition feature-card">
                        <div class="flex items-center mb-3">
                            <div class="bg-gray-200 p-2 rounded-full mr-3">
                                <i class="fas fa-check-circle text-gray-600 text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $session->subject->name ?? 'Tidak ada mata pelajaran' }}</h4>
                                <p class="text-sm text-gray-600">{{ $session->date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 mb-3">
                            <p><strong>Hari:</strong> {{ ucfirst($session->day_of_week) }}</p>
                            <p><strong>Jam:</strong> {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</p>
                            <p><strong>Guru:</strong> {{ $session->teacher->name ?? 'Tidak ada guru' }}</p>
                        </div>
                        <a href="{{ route('lms.show_session', $session) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                           aria-label="Lihat detail sesi {{ $session->subject->name }}">
                            <i class="fas fa-eye mr-2"></i> Lihat Sesi
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection