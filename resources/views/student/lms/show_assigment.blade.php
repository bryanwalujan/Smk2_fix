@extends('layouts.appstudent')

@section('title', 'Tugas - {{ $assignment->title }}')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <i class="fas fa-tasks text-amber-700 text-xl"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $assignment->title }}</h1>
            </div>
        </div>
    </div>

    <!-- Assignment Details -->
    <div class="card-section">
        <div class="card-body">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi</h2>
                <p class="text-gray-600">{{ $assignment->description ?? 'Tidak ada deskripsi' }}</p>
            </div>
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Tenggat Waktu</h2>
                <p class="text-gray-600 {{ $assignment->deadline->isPast() ? 'text-red-600' : '' }}">
                    {{ $assignment->deadline->translatedFormat('l, d F Y H:i') }}
                </p>
            </div>
            @if ($assignment->file_path)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">File Tugas</h2>
                    <a href="{{ Storage::url($assignment->file_path) }}"
                       class="btn-action-primary inline-flex items-center"
                       target="_blank">
                        <i class="fas fa-download mr-2"></i> Unduh File
                    </a>
                </div>
            @endif
            @if ($submission)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Pengumpulan Anda</h2>
                    <p class="text-gray-600">Dikumpulkan: {{ $submission->created_at->translatedFormat('l, d F Y H:i') }}</p>
                    @if ($submission->file_path)
                        <a href="{{ Storage::url($submission->file_path) }}"
                           class="btn-action-primary inline-flex items-center mt-2"
                           target="_blank">
                            <i class="fas fa-download mr-2"></i> Unduh Pengumpulan
                        </a>
                    @endif
                    @if ($submission->notes)
                        <p class="text-gray-600 mt-2"><strong>Catatan:</strong> {{ $submission->notes }}</p>
                    @endif
                </div>
            @else
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Kumpulkan Tugas</h2>
                    <a href="{{ route('lms.create_submission', $assignment) }}"
                       class="btn-action-amber inline-flex items-center">
                        <i class="fas fa-upload mr-2"></i> Kumpulkan
                    </a>
                </div>
            @endif
            <div class="flex justify-end">
                <a href="{{ route('lms.subject_sessions', $subject) }}"
                   class="btn-secondary flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .card-section {
        @apply bg-white rounded-xl shadow-lg overflow-hidden;
    }
    .card-body {
        @apply p-6;
    }
    .btn-action-primary {
        @apply inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700;
    }
    .btn-action-amber {
        @apply inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50;
    }
</style>

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