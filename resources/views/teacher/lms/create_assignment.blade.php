@extends('layouts.appguru')

@section('title', 'Tambah Tugas')

@section('content')
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Tugas Baru</h1>
            <div class="flex items-center text-sm text-gray-600 mt-1">
                <a href="{{ route('teacher.lms.show_session', $classSession) }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Sesi Kelas
                </a>
            </div>
        </div>
    </div>

    <!-- Session Info -->
    <div class="bg-blue-50 p-4 rounded-lg mb-6 border-l-4 border-blue-500">
        <h2 class="font-semibold text-gray-800">{{ $classSession->title }}</h2>
        <p class="text-sm text-gray-600">{{ $classSession->classroom->full_name }} - {{ $classSession->subject_name }}</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="font-medium">Ada masalah dengan input Anda:</p>
            </div>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <form method="POST" action="{{ route('teacher.lms.store_assignment', $classSession) }}">
            @csrf
            
            <!-- Title Field -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas*</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       required>
                <p class="mt-1 text-xs text-gray-500">Beri judul yang jelas untuk tugas ini</p>
            </div>

            <!-- Description Field -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Tugas</label>
                <textarea name="description" id="description" rows="5"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Berikan instruksi yang jelas untuk siswa</p>
            </div>

            <!-- Deadline Field -->
            <div class="mb-6">
                <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Tenggat Waktu*</label>
                <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       required>
                <p class="mt-1 text-xs text-gray-500">Pilih tanggal dan waktu batas pengumpulan</p>
            </div>

            <!-- File Upload Field (optional addition) -->
            <div class="mb-6">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Pendukung (opsional)</label>
                <div class="mt-1 flex items-center">
                    <input type="file" name="file" id="file" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Format: PDF, DOC, PPT, JPG, PNG (maks 256 MB)
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Simpan Tugas
                </button>
            </div>
        </form>
    </div>
@endsection