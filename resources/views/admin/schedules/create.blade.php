<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal - {{ $classroom->full_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="flex items-center mb-6">
            <a href="{{ route('classrooms.show', $classroom) }}" 
               class="mr-4 p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Tambah Jadwal untuk <span class="text-blue-600">{{ $classroom->full_name }}</span>
            </h1>
        </div>

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 text-red-700 border-l-4 border-red-500 rounded-lg flex items-start">
                <i class="fas fa-exclamation-circle mt-1 mr-3 flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('admin.schedules.store', $classroom) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Subject Field -->
                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran *</label>
                        <select id="subject_id" name="subject_id" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach ($subjects as $id => $name)
                                <option value="{{ $id }}" {{ old('subject_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Teacher Field -->
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">Guru *</label>
                        <select id="teacher_id" name="teacher_id" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">Pilih Guru</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Day Field -->
                    <div>
                        <label for="day" class="block text-sm font-medium text-gray-700 mb-1">Hari *</label>
                        <select id="day" name="day" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">Pilih Hari</option>
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                                <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                        @error('day')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai *</label>
                        <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Time Fields -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai *</label>
                        <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-4">
                    <a href="{{ route('classrooms.show', $classroom) }}" 
                       class="flex items-center gap-2 px-5 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </a>
                    <button type="submit" 
                            class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-sm">
                        <i class="fas fa-save"></i>
                        <span>Simpan Jadwal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>