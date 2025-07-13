@extends('layouts.appstudent')

@section('title', $material->title . ' - Materi Pembelajaran')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 rounded-lg flex-shrink-0">
                        <i class="fas fa-book-open text-green-700 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 break-words">{{ $material->title }}</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-book mr-1 text-green-500"></i> 
                            {{ $subject->name }}
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('lms.subject_sessions', $subject) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Content Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 md:p-8">
            <!-- Description Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas fa-align-left text-green-600 mr-2"></i>
                    Deskripsi Materi
                </h2>
                <div class="prose max-w-none text-gray-600">
                    @if($material->content)
                         {!! $material->content !!}
                    @else
                        <p class="text-gray-400 italic">Tidak ada deskripsi materi</p>
                    @endif
                </div>
            </div>

            <!-- File Attachment Section -->
            @if ($material->file_path)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas fa-paperclip text-green-600 mr-2"></i>
                    Lampiran Materi
                </h2>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <div class="flex items-center gap-3">
        <div class="p-2 bg-green-100 rounded-lg text-green-600">
            <i class="fas fa-file-alt text-lg"></i>
        </div>
        <div>
            <p class="font-medium text-gray-700 break-all">
                {{ basename($material->file_path) }}
            </p>
            @if(Storage::exists($material->file_path))
                <p class="text-sm text-gray-500">
                    {{ number_format(Storage::size($material->file_path) / 1024, 2) }} KB
                </p>
            @endif
        </div>
    </div>
    <a href="{{ Storage::url($material->file_path) }}"
       target="_blank"
       class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
        <i class="fas fa-download mr-2"></i> Unduh File
    </a>
</div>
            </div>
            @endif

            <!-- Content Section -->
            
            <!-- Metadata Section -->
            <div class="pt-4 border-t border-gray-200">
                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-green-500"></i>
                        Dibuat pada: {{ $material->created_at->translatedFormat('l, d F Y') }}
                    </div>
                    @if($material->updated_at != $material->created_at)
                    <div class="flex items-center">
                        <i class="fas fa-sync-alt mr-2 text-green-500"></i>
                        Diperbarui: {{ $material->updated_at->diffForHumans() }}
                    </div>
                    @endif
                </div>
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