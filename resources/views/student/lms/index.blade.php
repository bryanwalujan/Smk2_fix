@extends('layouts.appstudent')

@section('title', 'Dashboard LMS Siswa')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard LMS Siswa</h1>
            <p class="text-sm md:text-base text-gray-600 mt-1 md:mt-2">Selamat datang di Learning Management System</p>
            <div class="mt-2 flex items-center text-gray-600">
                <i class="fas fa-calendar-day mr-2"></i>
            </div>
        </div>
        <a href="{{ route('student.dashboard') }}" 
           class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
           aria-label="Kembali ke dashboard siswa">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Subjects List -->
    <div class="mb-12">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">Daftar Mata Pelajaran</h2>
        @if ($subjects->isEmpty())
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm md:text-base">
                <p>Tidak ada mata pelajaran tersedia.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($subjects as $subject)
                    <a href="{{ route('lms.subject_sessions', $subject->id) }}"
                       class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition feature-card flex items-center"
                       aria-label="Lihat pertemuan untuk {{ $subject->name }}">
                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                            <i class="fas fa-book text-blue-600 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $subject->name }}</h3>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection