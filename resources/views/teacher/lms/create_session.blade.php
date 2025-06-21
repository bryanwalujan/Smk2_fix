@extends('layouts.appguru')

@section('title', 'Tambah Sesi Kelas')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-sm">
        <!-- Navigation -->
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('teacher.lms.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Sesi Kelas</h2>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <h3 class="font-bold mb-2">Terjadi kesalahan:</h3>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('teacher.lms.store_session') }}">
            @csrf
            
            <!-- Classroom Selection -->
            <div class="mb-4">
                <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="classroom_id" id="classroom_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kelas</option>
                    @foreach ($classrooms as $id => $full_name)
                        <option value="{{ $id }}" {{ old('classroom_id') == $id ? 'selected' : '' }}>
                            {{ $full_name }}
                        </option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject Selection -->
            <div class="mb-4">
                <label for="subject_name" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="subject_name" id="subject_name" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject }}" {{ old('subject_name') == $subject ? 'selected' : '' }}>
                            {{ $subject }}
                        </option>
                    @endforeach
                </select>
                @error('subject_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Day of Week -->
            <div class="mb-4">
                <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-1">Hari</label>
                <select name="day_of_week" id="day_of_week" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                        <option value="{{ $day }}" {{ old('day_of_week') == $day ? 'selected' : '' }}>
                            {{ $day }}
                        </option>
                    @endforeach
                </select>
                @error('day_of_week')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start Time -->
            <div class="mb-4">
                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                <input type="time" name="start_time" id="start_time" 
                    value="{{ old('start_time') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('start_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- End Time -->
            <div class="mb-6">
                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                <input type="time" name="end_time" id="end_time" 
                    value="{{ old('end_time') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('end_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Sesi
                </button>
            </div>
        </form>
    </div>
@endsection