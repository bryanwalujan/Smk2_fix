@extends('layouts.appguru')

@section('title', 'Tambah Tugas')

@section('content')
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Tambah Tugas Baru</h1>
            <div class="flex items-center text-sm text-gray-600 mt-1">
                <a href="{{ route('teacher.lms.class_schedules', $classSession->classroom_id) }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Sesi Kelas
                </a>
            </div>
        </div>
    </div>

    <!-- Session Info Card -->
    <div class="bg-blue-50 p-4 rounded-xl mb-8 border border-blue-100 shadow-sm">
        <div class="flex items-start">
            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                <i class="fas fa-calendar-alt text-blue-600"></i>
            </div>
            <div>
                <h2 class="font-semibold text-gray-800">{{ $classSession->subject->name }}</h2>
                <p class="text-sm text-gray-600">
                    {{ $classSession->classroom->full_name }} | 
                    {{ $classSession->day }}, {{ $classSession->start_time->format('H:i') }} - {{ $classSession->end_time->format('H:i') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                <h3 class="font-medium text-red-800">Ada masalah dengan input Anda</h3>
            </div>
            <ul class="mt-2 pl-5 list-disc text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('teacher.lms.store_assignment', $classSession) }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Title Field -->
            <div class="p-6 border-b border-gray-200">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas*</label>
                <div class="relative">
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                           placeholder="Masukkan judul tugas" required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-heading text-gray-400"></i>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Contoh: "Tugas Akhir Bab 3 - Trigonometri"</p>
            </div>

            <!-- Description Field -->
            <div class="p-6 border-b border-gray-200">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Tugas</label>
                <textarea name="description" id="description" rows="5"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                          placeholder="Tambahkan instruksi tugas...">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Gunakan format jelas dan spesifik untuk instruksi</p>
            </div>

            <!-- Deadline Field -->
            <div class="p-6 border-b border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tenggat Waktu*</label>
                <div class="relative">
                    <input type="text" name="deadline" id="deadline" value="{{ old('deadline') }}"
                           class="datepicker-input w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                           placeholder="Pilih tanggal dan waktu" required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="far fa-calendar-alt text-gray-400"></i>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Batas akhir pengumpulan tugas</p>
            </div>

            <!-- File Upload Field -->
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">File Pendukung (opsional)</label>
                <div class="flex items-center justify-center w-full">
                    <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                            </p>
                            <p class="text-xs text-gray-500">
                                PDF, DOC, PPT, JPG, PNG (maks 256MB)
                            </p>
                        </div>
                        <input id="file" name="file" type="file" class="hidden">
                    </label>
                </div>
                <div id="file-name" class="mt-1 text-sm text-gray-500 hidden">
                    <i class="fas fa-paperclip mr-1"></i>
                    <span class="file-name"></span>
                    <button type="button" class="text-red-500 ml-2" onclick="clearFileInput('file')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="p-6 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-end gap-3">
                    <button type="reset" 
                            class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan Tugas
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Include Flatpickr CSS/JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        // Initialize datepicker
        flatpickr(".datepicker-input", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            minDate: "today",
            locale: "id",
            // Additional config as needed
        });

        // File input handler
        document.getElementById('file').addEventListener('change', function(e) {
            const fileNameDisplay = document.getElementById('file-name');
            if (this.files.length > 0) {
                fileNameDisplay.querySelector('.file-name').textContent = this.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            } else {
                fileNameDisplay.classList.add('hidden');
            }
        });

        function clearFileInput(id) {
            const input = document.getElementById(id);
            input.value = '';
            document.getElementById(id + '-name').classList.add('hidden');
        }
    </script>

    <style>
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            background: none;
            display: none;
        }
        .flatpickr-input {
            background-color: transparent !important;
        }
    </style>
@endsection