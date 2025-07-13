@extends('layouts.appguru')

@section('title', 'Tambah Materi dan Tugas')

@section('content')
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Tambah Materi dan Tugas</h1>
            <div class="flex items-center text-sm text-gray-600 mt-1">
                <a href="{{ route('teacher.lms.class_schedules', $classSession->classroom_id) }}" class="text-blue-600 hover:text-blue-800 flex items-center transition-colors">
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
                @if ($errors->has('material.title'))
                    <li>{{ $errors->first('material.title') }}</li>
                @endif
                @if ($errors->has('material.file'))
                    <li>{{ $errors->first('material.file') }}</li>
                @endif
                @foreach ($errors->get('assignments.*.title') as $index => $error)
                    <li>{{ $error }} (Tugas {{ $index + 1 }})</li>
                @endforeach
                @foreach ($errors->get('assignments.*.deadline') as $index => $error)
                    <li>{{ $error }} (Tugas {{ $index + 1 }})</li>
                @endforeach
                @foreach ($errors->get('assignments.*.file') as $index => $error)
                    <li>{{ $error }} (Tugas {{ $index + 1 }})</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('teacher.lms.store_material', $classSession) }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Material Section -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-book-open text-blue-500 mr-2"></i>
                    Informasi Materi
                </h3>
                
                <!-- Material Title -->
                <div class="mb-6">
                    <label for="material_title" class="block text-sm font-medium text-gray-700 mb-2">Judul Materi*</label>
                    <div class="relative">
                        <input type="text" name="material[title]" id="material_title" value="{{ old('material.title') }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                               placeholder="Masukkan judul materi" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-heading text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Contoh: "Pengenalan Aljabar Linear"</p>
                </div>

                <!-- Material Content -->
                <div class="mb-6">
                    <label for="material_content" class="block text-sm font-medium text-gray-700 mb-2">Konten Materi</label>
                    <textarea name="material[content]" id="material_content" rows="5"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                              placeholder="Tambahkan penjelasan materi...">{{ old('material.content') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Anda bisa menggunakan format HTML untuk styling</p>
                </div>

                <!-- Material File Upload -->
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Materi</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="material_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                                </p>
                                <p class="text-xs text-gray-500">
                                    PDF, DOC, PPT, JPG, PNG, MP4 (maks 256MB)
                                </p>
                            </div>
                            <input id="material_file" name="material[file]" type="file" class="hidden" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.mkv">
                        </label>
                    </div>
                    <div id="material-file-name" class="mt-1 text-sm text-gray-500 hidden">
                        <i class="fas fa-paperclip mr-1"></i>
                        <span class="file-name"></span>
                        <button type="button" class="text-red-500 ml-2" onclick="clearFileInput('material_file')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Assignments Toggle -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-tasks text-green-500 mr-2"></i>
                        Tambahkan Tugas
                    </h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="toggle-assignments" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Aktifkan</span>
                    </label>
                </div>
                <p class="mt-2 text-sm text-gray-500">Anda bisa menambahkan satu atau lebih tugas terkait materi ini</p>
            </div>

            <!-- Assignments Section (Hidden by Default) -->
            <div id="assignments-section" class="hidden p-6 bg-gray-50">
                <div id="assignments-container">
                    <!-- Assignment fields will be added here dynamically -->
                </div>
                
                <!-- Add Assignment Button -->
                <button type="button" id="add-assignment" 
                        class="w-full py-2 px-4 border border-dashed border-gray-300 rounded-lg text-gray-500 hover:text-gray-700 hover:border-gray-400 hover:bg-gray-100 transition-colors flex items-center justify-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Tugas Lain
                </button>
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
                        <i class="fas fa-save mr-2"></i> Simpan Materi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Assignment Template (Hidden) -->
    <div id="assignment-template" class="hidden">
        <div class="assignment-group mb-6 p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-md font-medium text-gray-700 flex items-center">
                    <i class="fas fa-tasks text-green-500 mr-2"></i>
                    <span class="assignment-number">Tugas 1</span>
                </h4>
                <button type="button" class="remove-assignment text-sm text-red-600 hover:text-red-800 flex items-center transition-colors">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                </button>
            </div>
            
            <!-- Title -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas*</label>
                <div class="relative">
                    <input type="text" name="assignments[0][title]"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                           placeholder="Masukkan judul tugas" required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-heading text-gray-400"></i>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Instruksi Tugas</label>
                <textarea name="assignments[0][description]" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                          placeholder="Tambahkan instruksi tugas..."></textarea>
            </div>
            
            <!-- Deadline -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tenggat Waktu*</label>
                <div class="relative">
                    <input type="datetime-local" name="assignments[0][deadline]"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                           required>
                    <div class="absolute inset-y-0 right-0 align-items pr-3 pointer-events-none">
                    </div>
                </div>
            </div>
            
            <!-- File -->
            
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File input handlers
            setupFileInput('material_file', 'material-file-name');
            
            // Toggle assignments section
            const toggleAssignments = document.getElementById('toggle-assignments');
            const assignmentsSection = document.getElementById('assignments-section');
            
            toggleAssignments.addEventListener('change', function() {
                if (this.checked) {
                    assignmentsSection.classList.remove('hidden');
                    addAssignmentField();
                } else {
                    assignmentsSection.classList.add('hidden');
                    document.getElementById('assignments-container').innerHTML = '';
                }
            });
            
            // Add assignment button
            document.getElementById('add-assignment').addEventListener('click', addAssignmentField);
            
            // Remove assignment handler
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-assignment')) {
                    const assignmentGroup = e.target.closest('.assignment-group');
                    assignmentGroup.remove();
                    updateAssignmentNumbers();
                    
                    // Hide section if no assignments left
                    if (document.querySelectorAll('.assignment-group').length === 0) {
                        toggleAssignments.checked = false;
                        assignmentsSection.classList.add('hidden');
                    }
                }
            });
        });
        
        let assignmentCount = 0;
        
        function addAssignmentField() {
            const container = document.getElementById('assignments-container');
            const template = document.getElementById('assignment-template').innerHTML;
            
            // Replace index placeholders with current count
            const newAssignment = template.replace(/\[0\]/g, `[${assignmentCount}]`);
            
            // Create new element
            const div = document.createElement('div');
            div.innerHTML = newAssignment;
            div.firstElementChild.classList.remove('hidden');
            
            // Add to container
            container.appendChild(div.firstElementChild);
            
            // Update file input handler for this assignment
            setupFileInput(`assignments[${assignmentCount}][file]`, div.querySelector('.assignment-file-name'));
            
            // Update assignment number
            assignmentCount++;
            updateAssignmentNumbers();
        }
        
        function updateAssignmentNumbers() {
            document.querySelectorAll('.assignment-group').forEach((group, index) => {
                group.querySelector('.assignment-number').textContent = `Tugas ${index + 1}`;
                
                // Show remove button only if there are multiple assignments
                const removeBtn = group.querySelector('.remove-assignment');
                removeBtn.style.display = document.querySelectorAll('.assignment-group').length > 1 ? 'flex' : 'none';
            });
        }
        
        function setupFileInput(inputId, displayId) {
            const input = document.getElementById(inputId);
            const display = document.getElementById(displayId);
            
            if (input && display) {
                input.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        display.querySelector('.file-name').textContent = this.files[0].name;
                        display.classList.remove('hidden');
                    } else {
                        display.classList.add('hidden');
                    }
                });
                
                // Setup clear button
                display.querySelector('button').addEventListener('click', function() {
                    input.value = '';
                    display.classList.add('hidden');
                });
            }
        }
        
        function clearFileInput(id) {
            const input = document.getElementById(id);
            input.value = '';
            document.getElementById(id + '-name').classList.add('hidden');
        }
    </script>

    <style>
        .assignment-group {
            transition: all 0.3s ease;
        }
    </style>
@endsection