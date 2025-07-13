@extends('layouts.appstudent')

@section('title', 'Dashboard LMS Siswa')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-indigo-700 text-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Selamat Belajar!</h1>
                    <p class="text-indigo-100 text-lg">Akses semua materi pembelajaran Anda di sini</p>
                    <div class="mt-4 flex items-center text-indigo-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span id="current-date" class="text-sm md:text-base">Memuat tanggal...</span>
                    </div>
                </div>
                <a href="{{ route('student.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 bg-white text-indigo-700 rounded-lg shadow hover:bg-indigo-50 transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   aria-label="Kembali ke dashboard siswa">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Subjects List -->
        <div class="mb-12">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Mata Pelajaran Anda</h2>
                <div class="relative w-full sm:w-64">
                    <input type="text" id="subject-search" placeholder="Cari mata pelajaran..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           aria-label="Cari mata pelajaran">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            @if ($subjects->isEmpty())
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">Tidak ada mata pelajaran yang tersedia saat ini.</p>
                        </div>
                    </div>
                </div>
            @else
                <div id="subjects-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($subjects as $subject)
                        <div class="subject-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1"
                             data-subject-name="{{ strtolower($subject->name) }}">
                            <a href="{{ route('lms.subject_sessions', $subject->id) }}"
                               class="block p-6 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               aria-label="Lihat sesi untuk {{ $subject->name }}">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $subject->name }}</h3>
                                </div>
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span>
                                        {{ \App\Models\ClassSession::where('subject_id', $subject->id)
                                            ->where('classroom_id', Auth::user()->student->classroom_id)
                                            ->count() }} Pertemuan
                                    </span>
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        Lihat Sesi
                                    </span>
                                </div>
                            </a>
                            <div class="p-6 pt-0 flex justify-between text-sm text-gray-500 border-t border-gray-200 mt-4">
                                <a href="{{ route('lms.subject_materials', $subject->id) }}"
                                   class="flex items-center hover:text-indigo-600 transition"
                                   aria-label="Lihat materi untuk {{ $subject->name }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Materi
                                </a>
                                <a href="{{ route('lms.subject_assignments', $subject->id) }}"
                                   class="flex items-center hover:text-indigo-600 transition"
                                   aria-label="Lihat tugas untuk {{ $subject->name }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Tugas
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Activity Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Aktivitas Terkini</h2>
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    @if ($recentActivities->isEmpty())
                        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm">Tidak ada aktivitas terkini saat ini.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($recentActivities as $activity)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full 
                                        {{ $activity['type'] === 'task_completed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }} 
                                        flex items-center justify-center mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            @if ($activity['type'] === 'task_completed')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            @endif
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-gray-800">{!! $activity['description'] !!}</p>
                                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Display current date with error handling
    document.addEventListener('DOMContentLoaded', () => {
        const dateElement = document.getElementById('current-date');
        if (dateElement) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            try {
                dateElement.textContent = new Date().toLocaleDateString('id-ID', options);
            } catch (error) {
                console.error('Error formatting date:', error);
                dateElement.textContent = 'Tanggal tidak tersedia';
            }
        }

        // Subject search functionality
        const searchInput = document.getElementById('subject-search');
        const subjectsGrid = document.getElementById('subjects-grid');
        const subjectCards = subjectsGrid ? subjectsGrid.querySelectorAll('.subject-card') : [];

        if (searchInput && subjectsGrid) {
            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();
                subjectCards.forEach(card => {
                    const subjectName = card.dataset.subjectName;
                    card.style.display = subjectName.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection