@extends('layouts.appstudent')

@section('title', 'Semua Tugas - {{ $subject->name }}')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <i class="fas fa-tasks text-amber-700 text-xl"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Semua Tugas - {{ $subject->name }}</h1>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    <div class="card-section">
        <div class="card-body">
            @if ($assignments->isEmpty())
                <div class="empty-state py-8">
                    <i class="far fa-clipboard text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada tugas</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($assignments->sortByDesc('deadline') as $assignment)
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
                                   class="btn-action-amber text-sm">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="mt-6 flex justify-end">
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
    .btn-action-amber {
        @apply inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50;
    }
    .empty-state {
        @apply flex flex-col items-center justify-center text-center;
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