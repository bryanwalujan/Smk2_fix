@extends('layouts.appstudent')

@section('title', 'Kumpulkan Tugas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700">
            <h2 class="text-2xl font-bold text-white">Kumpulkan Tugas: {{ $assignment->title }}</h2>
            <p class="text-blue-100 mt-1">Batas waktu: {{ $assignment->deadline ? $assignment->deadline->format('d M Y H:i') : 'Tidak ada batas waktu' }}</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-4 rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Terdapat {{ $errors->count() }} kesalahan dalam pengisian form:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Existing Submission Notice -->
        @if ($existingSubmission)
            <div class="p-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Anda sudah mengumpulkan tugas ini pada {{ $existingSubmission->created_at->format('d M Y H:i') }}.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <a href="{{ $subject_id ? route('lms.show_assignment', [$subject_id, $assignment->id]) : route('lms.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        Kembali ke Tugas
                    </a>
                </div>
            </div>
        @else
            <!-- Submission Form -->
            <form method="POST" action="{{ route('lms.store_submission', $assignment) }}" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <!-- File Upload Section -->
                <div class="mb-8">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-3">
                        File Tugas <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="mt-1 flex flex-col items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition duration-200">
                        <!-- File Preview Container -->
                        <div id="file-preview" class="hidden mb-4 text-center">
                            <div class="flex items-center justify-center bg-gray-100 p-3 rounded-lg">
                                <svg class="h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="ml-3 text-left">
                                    <p id="file-name" class="text-sm font-medium text-gray-900 truncate max-w-xs"></p>
                                    <p id="file-size" class="text-xs text-gray-500"></p>
                                </div>
                                <button type="button" id="remove-file" class="ml-2 text-red-500 hover:text-red-700">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Upload UI -->
                        <div id="upload-ui" class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex justify-center text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Pilih file</span>
                                    <input id="file" name="file" type="file" class="sr-only" accept=".pdf,.doc,.docx" required>
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PDF, DOC, atau DOCX 
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-3">
                        Catatan (Opsional)
                    </label>
                    <textarea id="notes" name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Tambahkan catatan untuk pengajar (jika perlu)">{{ old('notes') }}</textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between border-t pt-6">
                    <a href="{{ $subject_id ? route('lms.show_assignment', [$subject_id, $assignment->id]) : route('lms.index') }}" 
                       class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        Kembali
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Kumpulkan Tugas
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeFileBtn = document.getElementById('remove-file');
    const uploadUI = document.getElementById('upload-ui');

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            
            // Show file preview
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            filePreview.classList.remove('hidden');
            uploadUI.classList.add('hidden');
        }
    });

    // Handle file removal
    removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
        uploadUI.classList.remove('hidden');
    });

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Drag and drop functionality
    const dropArea = document.querySelector('.border-dashed');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropArea.classList.add('border-blue-400', 'bg-blue-50');
    }

    function unhighlight() {
        dropArea.classList.remove('border-blue-400', 'bg-blue-50');
    }

    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Trigger change event
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
});
</script>
@endsection