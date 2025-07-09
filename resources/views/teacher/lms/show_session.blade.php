@extends('layouts.appguru')

@section('title', 'Detail Sesi {{ $classSession->subject->name }}')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Utama -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <i class="fas fa-graduation-cap text-indigo-600 text-2xl"></i>
                        Sesi: {{ $classSession->subject->name ?? 'Tidak ada mata pelajaran' }}
                    </h1>
                    <div class="flex flex-wrap gap-4 mt-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-chalkboard mr-2 text-indigo-500"></i>
                            Kelas: {{ $classSession->classroom->full_name ?? 'Tidak ada kelas' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-book mr-2 text-indigo-500"></i>
                            Mata Pelajaran: {{ $classSession->subject->name ?? 'Tidak ada mata pelajaran' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar-day mr-2 text-indigo-500"></i>
                            Tanggal: {{ \Carbon\Carbon::parse($classSession->date)->translatedFormat('l, d F Y') }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-clock mr-2 text-indigo-500"></i>
                            Waktu: {{ \Carbon\Carbon::parse($classSession->start_time)->translatedFormat('H:i') }} - 
                            {{ \Carbon\Carbon::parse($classSession->end_time)->format('H:i') }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <a href="{{ route('teacher.lms.index') }}" 
                       class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Kembali ke Dashboard</span>
                    </a>
                    
                    <a href="{{ route('teacher.lms.show_attendance', $classSession) }}" 
                       class="btn-action-primary">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="hidden md:inline">Kelola Absensi</span>
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
                        <a href="{{ route('teacher.lms.create_material', $classSession) }}" 
                           class="btn-action-icon text-green-700 hover:bg-green-100" title="Tambah Materi">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if ($materials->isEmpty())
                    <div class="empty-state py-10">
                        <i class="far fa-folder-open text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada materi pembelajaran</p>
                        <a href="{{ route('teacher.lms.create_material', $classSession) }}" 
                           class="btn-action-primary mt-4">
                            <i class="fas fa-plus mr-2"></i> Tambah Materi
                        </a>
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
                                        <a href="{{ route('teacher.lms.show_material', [$classSession, $material]) }}" 
                                           class="btn-icon text-green-600 hover:bg-green-50" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('teacher.lms.edit_material', [$classSession, $material]) }}" 
                                           class="btn-icon text-blue-600 hover:bg-blue-50" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('teacher.lms.destroy_material', [$classSession, $material]) }}" method="POST">
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
                        <a href="{{ route('teacher.lms.create_assignment', $classSession) }}" 
                           class="btn-action-icon text-amber-700 hover:bg-amber-100" title="Tambah Tugas">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if ($assignments->isEmpty())
                    <div class="empty-state py-10">
                        <i class="far fa-clipboard text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada tugas</p>
                        <a href="{{ route('teacher.lms.create_assignment', $classSession) }}" 
                           class="btn-action-amber mt-4">
                            <i class="fas fa-plus mr-2"></i> Buat Tugas
                        </a>
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
                                            <a href="{{ route('teacher.lms.show_assignment', [$classSession, $assignment]) }}" 
                                               class="btn-icon text-blue-600 hover:bg-blue-50" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.lms.edit_assignment', [$classSession, $assignment]) }}" 
                                               class="btn-icon text-blue-600 hover:bg-blue-50" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teacher.lms.destroy_assignment', [$classSession, $assignment]) }}" method="POST">
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
                                            <a href="{{ route('teacher.lms.show_assignment', [$classSession, $assignment]) }}" 
                                               class="btn-action-outline">
                                                <i class="fas fa-info-circle  mr-2"></i> Detail
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

    <!-- Jadwal Pertemuan -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-full">
                        <i class="fas fa-calendar-day text-indigo-700 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Detail Pertemuan</h2>
                </div>
                <span class="badge-indigo">
                    1 Sesi
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg hover:border-indigo-300 transition-all duration-200">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="text-3xl font-bold text-indigo-600">
                            {{ \Carbon\Carbon::parse($classSession->date)->translatedFormat('d') }}
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($classSession->date)->translatedFormat('l') }}
                            </div>
                            <div class="text-gray-700 font-semibold">
                                {{ $classSession->subject ? $classSession->subject->name : 'Tidak Ada' }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-clock text-indigo-500"></i>
                        <span>{{ \Carbon\Carbon::parse($classSession->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($classSession->end_time)->format('H:i') }}</span>
                    </div>
                </div>
            </div>
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