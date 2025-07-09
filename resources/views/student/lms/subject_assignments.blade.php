@extends('layouts.appstudent')

@section('title', 'Semua Tugas - {{ $subject->name }}')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <div class="p-6 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-100 rounded-full">
                    <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Semua Tugas - {{ $subject->name }}</h1>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6">
            @if ($assignments->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-lg text-gray-500">Belum ada tugas untuk {{ $subject->name }}</p>
                    <a href="{{ route('lms.subject_sessions', $subject) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali ke Sesi
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($assignments->sortByDesc('created_at') as $assignment)
                        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition-shadow duration-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $assignment->title }}</h3>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Tenggat: {{ $assignment->deadline->translatedFormat('l, d F Y H:i') }}
                                    </div>
                                    <span class="text-xs text-gray-500 mt-2 block">
                                        Diposting pada {{ $assignment->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs px-2 py-1 rounded-full {{ $assignment->deadline->isPast() ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $assignment->deadline->translatedFormat('d M') }}
                                    </span>
                                    <a href="{{ route('lms.show_assignment', ['subject' => $subject->id, 'assignment' => $assignment->id]) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white text-sm font-medium rounded-md hover:bg-amber-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('lms.subject_sessions', $subject) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali ke Sesi
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SweetAlert2 Integration -->
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4f46e5',
                background: '#ffffff',
                showClass: { popup: 'animate__animated animate__fadeIn' },
                hideClass: { popup: 'animate__animated animate__fadeOut' },
                timer: 3000,
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
                hideClass: { popup: 'animate__animated animate__fadeOut' },
                timer: 4000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection