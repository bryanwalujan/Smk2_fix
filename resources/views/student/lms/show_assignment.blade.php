@extends('layouts.appstudent')

@section('title', $assignment->title . ' - Detail Tugas')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 border border-gray-100">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 rounded-lg flex-shrink-0">
                        <i class="fas fa-tasks text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 break-words">{{ $assignment->title }}</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-book mr-1 text-blue-500"></i> 
                            {{ $subject->name }}
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('lms.subject_sessions', $subject) }}"
                       class="inline-flex items-center px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Content Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 border border-gray-100">
        <div class="p-6 md:p-8 space-y-8">
            <!-- Description Section -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-align-left text-blue-500 mr-3"></i>
                    Deskripsi Tugas
                </h2>
                <div class="prose max-w-none text-gray-600 pl-9">
                    @if($assignment->description)
                        {!! nl2br(e($assignment->description)) !!}
                    @else
                        <p class="text-gray-400 italic">Tidak ada deskripsi tugas</p>
                    @endif
                </div>
            </div>

            <!-- Deadline Section -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clock text-blue-500 mr-3"></i>
                    Tenggat Waktu
                </h2>
                <div class="flex items-center gap-4 p-4 rounded-lg border {{ $assignment->deadline->isPast() ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} pl-9">
                    <div class="p-2 {{ $assignment->deadline->isPast() ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }} rounded-lg">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <p class="font-medium {{ $assignment->deadline->isPast() ? 'text-red-700' : 'text-blue-700' }}">
                            {{ $assignment->deadline->translatedFormat('l, d F Y') }}
                        </p>
                        <p class="text-sm {{ $assignment->deadline->isPast() ? 'text-red-600' : 'text-blue-600' }}">
                            {{ $assignment->deadline->translatedFormat('H:i') }} WIB
                            @if($assignment->deadline->isPast())
                                <span class="ml-3 px-2.5 py-1 text-xs bg-red-100 text-red-800 rounded-full">Melewati Tenggat</span>
                            @else
                                <span class="ml-3 px-2.5 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Sisa: {{ $assignment->deadline->diffForHumans() }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Assignment Files Section -->
            @if ($assignment->file_path)
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-paperclip text-blue-500 mr-3"></i>
                    File Tugas
                </h2>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200 pl-9">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                            <i class="fas fa-file-alt text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700 break-all">
                                {{ basename($assignment->file_path) }}
                            </p>
                            @if(Storage::exists($assignment->file_path))
                                <p class="text-sm text-gray-500">
                                    {{ number_format(Storage::size($assignment->file_path) / 1024, 2) }} KB
                                </p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ Storage::url($assignment->file_path) }}"
                       target="_blank"
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i> Unduh File
                    </a>
                </div>
            </div>
            @endif

            <!-- Submission Section -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-upload text-blue-500 mr-3"></i>
                    Pengumpulan Tugas
                </h2>
                
                @if ($submission)
                    <div class="p-6 bg-green-50 rounded-lg border border-green-200 space-y-4 pl-9">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-green-100 rounded-lg text-green-600">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-green-800">Tugas Telah Dikumpulkan</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $submission->created_at->translatedFormat('l, d F Y H:i') }}
                                </p>
                            </div>
                        </div>

                        @if ($submission->file_path)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                    <i class="fas fa-file-upload text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700 break-all">
                                        {{ basename($submission->file_path) }}
                                    </p>
                                    @if(Storage::exists($submission->file_path))
                                        <p class="text-sm text-gray-500">
                                            {{ number_format(Storage::size($submission->file_path) / 1024, 2) }} KB
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ Storage::url($submission->file_path) }}"
                               target="_blank"
                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-2"></i> Unduh
                            </a>
                        </div>
                        @endif

                        @if ($submission->notes)
                        <div class="p-4 bg-white rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Catatan Anda:</h4>
                            <p class="text-gray-600">{{ $submission->notes }}</p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 bg-blue-50 rounded-lg border border-blue-200 flex flex-col md:flex-row md:items-center justify-between gap-4 pl-9">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                <i class="fas fa-exclamation-circle text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-blue-800">Belum Mengumpulkan</h3>
                                <p class="text-sm text-gray-600">
                                    @if($assignment->deadline->isPast())
                                        Tenggat waktu telah berakhir
                                    @else
                                    Sisa waktu: {{ $assignment->deadline->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('lms.create_submission', $assignment) }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                            <i class="fas fa-upload mr-2"></i> Kumpulkan Tugas
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Script -->
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
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
                showClass: { popup: 'animate__animated animate__shakeX' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                timer: 5000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection