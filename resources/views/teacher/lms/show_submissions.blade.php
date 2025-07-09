@extends('layouts.appguru')

@section('title', 'Pengumpulan Tugas')

@section('content')
<!-- Include SweetAlert2 CSS -->


<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-clipboard-list text-indigo-600"></i>
                    Pengumpulan Tugas: {{ $assignment->title }}
                </h2>
                @if($classSession)
                    <a href="{{ route('teacher.lms.class_schedules', $classSession->classroom_id) }}" 
                       class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Kembali ke Sesi</span>
                    </a>
                @else
                    <a href="{{ route('teacher.lms.index') }}" 
                       class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Kembali ke Dashboard</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div id="success-alert" class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div id="error-alert" class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-700">Daftar Pengumpulan</h3>
                <div class="overflow-x-auto mt-2">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pengumpulan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($assignment->submissions as $submission)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->nis }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->classroom->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('d F Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($submission->file_path)
                                            <a href="{{ Storage::url($submission->file_path) }}" target="_blank" 
                                               class="text-indigo-600 hover:text-indigo-900">Unduh</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $submission->notes ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $submission->score ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="{{ route('teacher.lms.grade_submission', [$assignment, $submission]) }}" 
                                              method="POST" class="inline-flex items-center space-x-2 grade-submission-form">
                                            @csrf
                                            <input type="number" name="score" value="{{ $submission->score ?? '' }}" 
                                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                                   min="0" max="100" placeholder="0-100">
                                            <button type="submit" 
                                                    class="btn-action-primary">
                                                <i class="fas fa-check mr-2"></i>
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada pengumpulan tugas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 JS -->
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // SweetAlert2 for form submission confirmation
    const forms = document.querySelectorAll('.grade-submission-form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Nilai akan disimpan untuk pengumpulan ini.',
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
    });

    // SweetAlert2 for success/error messages, only show if not from back/forward navigation
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

    @if (session('error'))
        if (window.performance && window.performance.navigation.type !== 2) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
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
            session()->forget('error');
        @endphp
    @endif
});
</script>

<style>
    .btn-action-primary {
        @apply inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
    }

    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200;
    }
</style>

<!-- Prevent browser caching -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endsection