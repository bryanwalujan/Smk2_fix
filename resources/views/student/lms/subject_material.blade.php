@extends('layouts.appstudent')

@section('title', 'Semua Materi - {{ $subject->name }}')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-book-open text-green-700 text-xl"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Semua Materi - {{ $subject->name }}</h1>
            </div>
        </div>
    </div>

    <!-- Materials List -->
    <div class="card-section">
        <div class="card-body">
            @if ($materials->isEmpty())
                <div class="empty-state py-8">
                    <i class="far fa-folder-open text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada materi pembelajaran</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($materials as $material)
                        <div class="border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-800">{{ $material->title }}</h3>
                            @if ($material->description)
                                <p class="text-sm text-gray-600 mt-1 truncate">
                                    {{ $material->description }}
                                </p>
                            @endif
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    {{ $material->created_at->diffForHumans() }}
                                </span>
                                <a href="{{ route('lms.show_material', [$subject, $material]) }}" 
                                   class="btn-action-primary text-sm">
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
    .btn-action-primary {
        @apply inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700;
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