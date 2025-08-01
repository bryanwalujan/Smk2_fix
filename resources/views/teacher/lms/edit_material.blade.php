@extends('layouts.appguru')

@section('title', 'Edit Materi')

@section('content')



<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-book-open text-green-600 text-2xl"></i>
                Edit Materi
            </h1>
        </div>
        <div class="p-6">
            <form id="edit-material-form" action="{{ route('teacher.lms.update_material', [$classSession, $material]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Materi</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $material->title) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           required maxlength="100">
                    @error('title')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">Konten</label>
                    <textarea name="content" id="content" rows="6"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('content', $material->content) }}</textarea>
                    @error('content')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700">Lampiran (PDF, DOC, PPT, Gambar, Video)</label>
                    <input type="file" name="file" id="file"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    @if ($material->file_path)
                        <p class="mt-2 text-sm text-gray-600">File saat ini: <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="text-green-600 hover:underline">Lihat file</a></p>
                    @endif
                    @error('file')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('teacher.lms.class_schedules', $classSession->classroom_id) }}"
                       class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Batal
                    </a>
                    <button type="submit" class="btn-action-primary">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 JS -->
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // SweetAlert2 for form submission confirmation
    const form = document.getElementById('edit-material-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Perubahan pada materi akan disimpan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }

    // SweetAlert2 for success message, only show if not from back/forward navigation
    @if (session('success'))
        if (window.performance && window.performance.navigation.type !== 2) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear session flash message via AJAX
                    fetch('{{ route('teacher.lms.clear_flash') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).catch(error => console.error('Error clearing flash message:', error));
                }
            });
        }
        @php
            session()->forget('success');
        @endphp
    @endif
});
</script>

<style>
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
    }

    .btn-action-primary {
        @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
    }
</style>

<!-- Prevent browser caching -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endsection