@extends('layouts.appstudent')

@section('title', 'Detail Sesi {{ $classSession->subject->name }}')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Sesi: {{ $classSession->subject->name ?? 'Tidak ada mata pelajaran' }}</h2>
                    <div class="mt-2 space-y-1">
                        <p class="text-gray-600">
                            <span class="font-medium">Kelas:</span> {{ $classSession->classroom->full_name ?? 'Tidak ada kelas' }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Mata Pelajaran:</span> {{ $classSession->subject->name ?? 'Tidak ada mata pelajaran' }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Tanggal:</span> {{ \Carbon\Carbon::parse($classSession->date)->translatedFormat('l, d F Y') }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Waktu:</span> 
                            {{ \Carbon\Carbon::parse($classSession->start_time)->translatedFormat('H:i') }} - 
                            {{ \Carbon\Carbon::parse($classSession->end_time)->format('H:i') }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Guru:</span> {{ $classSession->teacher->name ?? 'Tidak ada guru' }}
                        </p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('lms.subject_sessions', $classSession->subject_id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kembali ke Pertemuan
                    </a>
                    <a href="{{ route('lms.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kembali ke LMS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection