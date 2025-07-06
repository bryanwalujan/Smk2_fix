@extends('layouts.appguru')

@section('title', 'Jadwal Kelas {{ $classroom->full_name }}')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Utama -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <i class="fas fa-graduation-cap text-indigo-600 text-2xl"></i>
                        Jadwal Kelas {{ $classroom->full_name }}
                    </h1>
                    <div class="flex flex-wrap gap-4 mt-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-clock mr-2 text-indigo-500"></i>
                            Hari: 
                            @php
                                $uniqueDays = $classSessions->unique('day_of_week')->pluck('day_of_week')->toArray();
                            @endphp
                            {{ implode(', ', $uniqueDays) ?: 'Tidak ada jadwal' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-book mr-2 text-indigo-500"></i>
                            Mata Pelajaran: 
                            @php
                                $uniqueSubjects = $classSessions->pluck('subject.name')->filter()->unique()->toArray();
                            @endphp
                            {{ implode(', ', $uniqueSubjects) ?: 'Tidak ada mata pelajaran' }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <a href="{{ route('teacher.lms.index') }}" 
                       class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Kembali ke Dashboard</span>
                    </a>
                    <a href="{{ route('teacher.lms.class_submissions_export', $classroom->id) }}" 
                       class="btn-success">
                        <i class="fas fa-file-export mr-2"></i>
                        <span class="hidden md:inline">Unduh Nilai</span>
                    </a>
                    <a href="{{ route('teacher.lms.class_attendance_export', $classroom->id) }}" 
                       class="btn-success">
                        <i class="fas fa-file-export mr-2"></i>
                        <span class="hidden md:inline">Unduh Absensi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Dua Kolom (Materi dan Tugas) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Kolom Kiri: Materi Pembelajaran -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-book-open text-green-700 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800">Materi Pembelajaran</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge-green">
                            {{ $materials->count() }} Materi
                        </span>
                        @if ($classSessions->isNotEmpty())
                            <a href="{{ route('teacher.lms.create_material', $classSessions->first()) }}" 
                               class="btn-action-icon text-green-700 hover:bg-green-100" title="Tambah Materi">
                                <i class="fas fa-plus"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if ($materials->isEmpty())
                    <div class="empty-state py-10">
                        <i class="far fa-folder-open text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada materi pembelajaran</p>
                        @if ($classSessions->isNotEmpty())
                            <a href="{{ route('teacher.lms.create_material', $classSessions->first()) }}" 
                               class="btn-action-primary mt-4">
                                <i class="fas fa-plus mr-2"></i> Tambah Materi
                            </a>
                        @endif
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($materials as $material)
                            <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all duration-200">
                                <div class="flex flex-col sm:flex-row justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 text-lg">{{ $material->title }}</h3>
                                        @if ($material->content)
                                            <p class="mt-2 text-gray-600 text-sm line-clamp-2">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($material->content), 120) }}
                                            </p>
                                        @endif
                                        @if ($material->file_path)
                                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" 
                                               class="inline-flex items-center mt-3 text-green-700 hover:text-green-800 text-sm font-medium">
                                                <i class="fas fa-paperclip mr-2"></i> Lampiran Materi
                                            </a>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('teacher.lms.show_material', [$classSessions->isNotEmpty() ? $classSessions->first() : 0, $material]) }}" 
                                           class="btn-icon text-green-600 hover:bg-green-50" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('teacher.lms.edit_material', [$classSessions->isNotEmpty() ? $classSessions->first() : 0, $material]) }}" 
                                           class="btn-icon text-blue-600 hover:bg-blue-50" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('teacher.lms.destroy_material', [$classSessions->isNotEmpty() ? $classSessions->first() : 0, $material]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon text-red-600 hover:bg-red-50" 
                                                    title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Tugas -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <div class="p-6 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 rounded-full">
                            <i class="fas fa-tasks text-amber-700 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800">Daftar Tugas</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge-amber">
                            {{ $assignments->count() }} Tugas
                        </span>
                        @if ($classSessions->isNotEmpty())
                            <a href="{{ route('teacher.lms.create_assignment', $classSessions->first()) }}" 
                               class="btn-action-icon text-amber-700 hover:bg-amber-100" title="Tambah Tugas">
                                <i class="fas fa-plus"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if ($assignments->isEmpty())
                    <div class="empty-state py-10">
                        <i class="far fa-clipboard text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada tugas</p>
                        @if ($classSessions->isNotEmpty())
                            <a href="{{ route('teacher.lms.create_assignment', $classSessions->first()) }}" 
                               class="btn-action-amber mt-4">
                                <i class="fas fa-plus mr-2"></i> Buat Tugas
                            </a>
                        @endif
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($assignments as $assignment)
                            <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all duration-200">
                                <div class="flex flex-col sm:flex-row justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 text-lg">{{ $assignment->title }}</h3>
                                        <div class="mt-2 text-sm text-amber-600 font-medium flex items-center gap-2">
                                            <i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d F Y, H:i') }}
                                        </div>
                                        @if ($assignment->description)
                                            <p class="mt-2 text-gray-600 text-sm line-clamp-2">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($assignment->description), 100) }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                        <div class="flex gap-2">
                                            <a href="{{ route('teacher.lms.show_submissions', $assignment) }}" 
                                               class="btn-icon text-amber-600 hover:bg-amber-50" title="Pengumpulan">
                                                <i class="fas fa-list-check"></i>
                                            </a>
                                            <a href="{{ route('teacher.lms.show_assignment', [$classSessions->isNotEmpty() ? $classSessions->first() : 0, $assignment]) }}" 
                                               class="btn-icon text-blue-600 hover:bg-blue-50" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.lms.edit_assignment', [$classSessions->isNotEmpty() ? $classSessions->first() : 0, $assignment]) }}" 
                                               class="btn-icon text-blue-600 hover:bg-blue-50" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teacher.lms.destroy_assignment', [$classSessions->isNotEmpty() ? $classSessions->first() : 0, $assignment]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon text-red-600 hover:bg-red-50" 
                                                        title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="flex gap-2 mt-2 sm:mt-0">
                                            <a href="{{ route('teacher.lms.show_assignment', [$classSessions->isNotEmpty() ? $classSessions->first() : 0, $assignment]) }}" 
                                               class="btn-action-outline">
                                                <i class="fas fa-info-circle mr-2"></i> Detail
                                            </a>
                                            <a href="{{ route('teacher.lms.show_submissions', $assignment) }}" 
                                               class="btn-action-amber">
                                                <i class="fas fa-list-check mr-2"></i> Pengumpulan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Jadwal Kelas (Bagian Bawah) -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-full">
                        <i class="fas fa-calendar-day text-indigo-700 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Jadwal Pertemuan</h2>
                </div>
                <span class="badge-indigo">
                    {{ $classSessions->count() }} Sesi
                </span>
            </div>
        </div>
        <div class="p-6">
            @if ($classSessions->isEmpty())
                <div class="empty-state py-10">
                    <i class="far fa-calendar-plus text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada jadwal untuk kelas ini</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($classSessions->sortBy('date')->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->date)->translatedFormat('F Y');
                    }) as $month => $sessions)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                {{ $month }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($sessions as $session)
                                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg hover:border-indigo-300 transition-all duration-200">
                                        <div class="flex flex-col gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="text-3xl font-bold text-indigo-600">
                                                    {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d') }}
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        {{ \Carbon\Carbon::parse($session->date)->translatedFormat('l') }}
                                                    </div>
                                                    <div class="text-gray-700 font-semibold">
                                                        {{ $session->subject ? $session->subject->name : 'Tidak Ada' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                <i class="fas fa-clock text-indigo-500"></i>
                                                <span>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                                            </div>
                                            <a href="{{ route('teacher.lms.show_session', $session->id) }}"
                                               class="btn-action-primary mt-2">
                                                <i class="fas fa-eye mr-2"></i> Detail Sesi
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

<style>
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
    }
    
    .btn-success {
        @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200;
    }
    
    .btn-action-primary {
        @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
    }
    
    .btn-action-amber {
        @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200;
    }
    
    .btn-action-outline {
        @apply inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
    }
    
    .btn-icon {
        @apply inline-flex items-center justify-center p-2 rounded-full text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200;
    }
    
    .badge-indigo {
        @apply text-xs font-semibold text-indigo-800 bg-indigo-100 px-3 py-1 rounded-full;
    }
    
    .badge-green {
        @apply text-xs font-semibold text-green-800 bg-green-100 px-3 py-1 rounded-full;
    }
    
    .badge-amber {
        @apply text-xs font-semibold text-amber-800 bg-amber-100 px-3 py-1 rounded-full;
    }
    
    .empty-state {
        @apply flex flex-col items-center justify-center text-center;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection