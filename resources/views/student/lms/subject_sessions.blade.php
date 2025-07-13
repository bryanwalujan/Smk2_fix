@extends('layouts.appstudent')

@section('title', $subject->name . ' - Materi & Pertemuan')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Utama -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-book text-indigo-600"></i>
                        {{ $subject->name }}
                    </h1>
                    <div class="flex flex-wrap gap-3 mt-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user-tie mr-2 text-indigo-500"></i>
                            Guru: {{ $teacherName }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>
                            Total Pertemuan: {{ $currentWeekSessions->count() + $upcomingSessions->count() + $pastSessions->count() }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('lms.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> 
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Materi dan Tugas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Kolom Materi -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-green-50 to-green-100 border-green-200 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-book-open text-green-700 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Materi Pembelajaran</h2>
                </div>
                <span class="text-xs font-medium text-green-800 bg-green-100 px-2.5 py-1 rounded-full">
                    {{ $materials->count() }} Materi
                </span>
            </div>

            <div class="p-6">
                @if ($materials->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center py-8">
                        <i class="far fa-folder-open text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada materi pembelajaran</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($materials->sortByDesc('created_at')->take(3) as $material)
                            <div class="border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <h3 class="font-medium text-gray-800">{{ $material->title }}</h3>
                                @if ($material->content)
                                    <p class="text-sm text-gray-600 mt-1 truncate">
                                        {{ strip_tags($material->content) }}
                                    </p>
                                @endif
                                <div class="mt-3 flex justify-between items-center">
                                    <span class="text-xs text-gray-500">
                                        {{ $material->created_at->diffForHumans() }}
                                    </span>
                                    <a href="{{ route('lms.show_material', [$subject, $material]) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        <i class="fas fa-eye mr-1"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        @if ($materials->count() > 3)
                            <div class="text-center mt-4">
                                <a href="{{ route('lms.subject_materials', $subject) }}" 
                                   class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Lihat Semua Materi
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Tugas -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-amber-50 to-amber-100 border-amber-200 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <i class="fas fa-tasks text-amber-700 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Tugas Terbaru</h2>
                </div>
                <span class="text-xs font-medium text-amber-800 bg-amber-100 px-2.5 py-1 rounded-full">
                    {{ $assignments->count() }} Tugas
                </span>
            </div>

            <div class="p-6">
                @if ($assignments->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center py-8">
                        <i class="far fa-clipboard text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada tugas</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($assignments->sortByDesc('created_at')->take(3) as $assignment)
                            <div class="border border-amber-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium text-gray-800">{{ $assignment->title }}</h3>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $assignment->deadline->isPast() ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $assignment->deadline->translatedFormat('d M') }}
                                    </span>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <i class="far fa-clock mr-1"></i>
                                    Tenggat: {{ $assignment->deadline->translatedFormat('l, d F Y H:i') }}
                                </div>
                                <div class="mt-3 flex justify-between items-center">
                                    <span class="text-xs text-gray-500">
                                        {{ $assignment->created_at->diffForHumans() }}
                                    </span>
                                    <a href="{{ route('lms.show_assignment', [$subject, $assignment]) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                                        <i class="fas fa-eye mr-1"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        @if ($assignments->count() > 3)
                            <div class="text-center mt-4">
                                <a href="{{ route('lms.subject_assignments', $subject) }}" 
                                   class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Lihat Semua Tugas
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pertemuan Minggu Ini -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-yellow-50 to-yellow-100 border-yellow-200 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-calendar-week text-yellow-700 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Pertemuan Minggu Ini</h2>
            </div>
            <span class="text-xs font-medium text-yellow-800 bg-yellow-100 px-2.5 py-1 rounded-full">
                {{ $currentWeekSessions->count() }} Sesi
            </span>
        </div>

        <div class="p-6">
            @if ($currentWeekSessions->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-8">
                    <i class="far fa-calendar-plus text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Tidak ada pertemuan minggu ini</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($currentWeekSessions->sortBy('date') as $session)
                        <div class="border border-yellow-200 rounded-lg p-5 hover:shadow-md transition-shadow bg-yellow-50">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d') }}
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($session->date)->translatedFormat('l') }}
                                    </div>
                                    <div class="text-gray-700">
                                        {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-yellow-500"></i>
                                    <span>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-chalkboard-teacher text-yellow-500"></i>
                                    <span>{{ $session->teacher->user->name ?? 'Tidak ada guru' }}</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('lms.show_session', $session) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 w-full text-center">
                                    <i class="fas fa-eye mr-1"></i> Lihat Sesi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Pertemuan yang Akan Datang -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-calendar-plus text-blue-700 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Pertemuan yang Akan Datang</h2>
            </div>
            <span class="text-xs font-medium text-blue-800 bg-blue-100 px-2.5 py-1 rounded-full">
                {{ $upcomingSessions->count() }} Sesi
            </span>
        </div>

        <div class="p-6">
            @if ($upcomingSessions->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-8">
                    <i class="far fa-calendar text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Tidak ada pertemuan yang akan datang</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($upcomingSessions->sortBy('date')->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->date)->translatedFormat('F Y');
                    }) as $month => $sessions)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                {{ $month }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($sessions as $session)
                                    <div class="border border-blue-200 rounded-lg p-5 hover:shadow-md transition-shadow bg-blue-50">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="text-2xl font-bold text-blue-600">
                                                {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d') }}
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($session->date)->translatedFormat('l') }}
                                                </div>
                                                <div class="text-gray-700">
                                                    {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-2 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-clock text-blue-500"></i>
                                                <span>
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-chalkboard-teacher text-blue-500"></i>
                                                <span>{{ $session->teacher->user->name ?? 'Tidak ada guru' }}</span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ route('lms.show_session', $session) }}"
                                               class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 w-full text-center">
                                                <i class="fas fa-eye mr-1"></i> Lihat Sesi
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Pertemuan yang Sudah Lewat -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100 border-gray-200 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <i class="fas fa-calendar-check text-gray-700 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Pertemuan yang Sudah Lewat</h2>
            </div>
            <span class="text-xs font-medium text-gray-800 bg-gray-100 px-2.5 py-1 rounded-full">
                {{ $pastSessions->count() }} Sesi
            </span>
        </div>

        <div class="p-6">
            @if ($pastSessions->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-8">
                    <i class="far fa-calendar-minus text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Tidak ada pertemuan yang sudah lewat</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($pastSessions->sortByDesc('date')->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->date)->translatedFormat('F Y');
                    }) as $month => $sessions)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                {{ $month }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($sessions as $session)
                                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow bg-gray-50">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="text-2xl font-bold text-gray-600">
                                                {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d') }}
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($session->date)->translatedFormat('l') }}
                                                </div>
                                                <div class="text-gray-700">
                                                    {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-2 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-clock text-gray-500"></i>
                                                <span>
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-chalkboard-teacher text-gray-500"></i>
                                                <span>{{ $session->teacher->user->name ?? 'Tidak ada guru' }}</span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ route('lms.show_session', $session) }}"
                                               class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 w-full text-center">
                                                <i class="fas fa-eye mr-1"></i> Lihat Sesi
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb',
                background: '#ffffff',
                showClass: { popup: 'animate__animated animate__fadeInDown' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' },
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
                background: '#ffffff',
                showClass: { popup: 'animate__animated animate__ShakeX' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                timer: 5000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection