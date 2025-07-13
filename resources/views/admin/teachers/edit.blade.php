<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guru | Sistem Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /* Main Layout */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        /* Card Container */
        .form-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        
        /* Form Elements */
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background-color: white;
        }
        
        .form-input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
            outline: none;
        }
        
        .form-hint {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background-color: #4f46e5;
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
        }
        
        .btn-secondary {
            background-color: white;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }
        
        /* Select2 Customizations */
        .select2-container {
            width: 100% !important;
            font-family: inherit;
        }
        
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            min-height: 42px;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.2s ease;
            background-color: white;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 2px 8px;
            margin: 0 6px 4px 0;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255, 255, 255, 0.8);
            margin-right: 4px;
            border-right: none;
            padding: 0;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: white;
        }
        
        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 0;
            height: 30px;
            padding: 0 4px;
            min-width: 120px;
        }
        
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            z-index: 1060 !important;
        }
        
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            font-size: 0.875rem;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #eef2ff;
            color: #4f46e5;
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f5f3ff;
            color: #4f46e5;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #818cf8 !important;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
        }
        
        .select2-container--default .select2-selection--single .select2-selection__clear,
        .select2-container--default .select2-selection--multiple .select2-selection__clear {
            color: #94a3b8;
            margin-right: 6px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__clear:hover,
        .select2-container--default .select2-selection--multiple .select2-selection__clear:hover {
            color: #64748b;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__placeholder,
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            color: #94a3b8;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 10px;
        }
        
        /* Error Handling */
        .error-message {
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 0.25rem;
        }
        
        .error-border {
            border-color: #f87171 !important;
        }
        
        /* Header Section */
        .form-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .form-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: #eef2ff;
            color: #4f46e5;
            margin-right: 1rem;
            font-size: 1.25rem;
        }
        
        .form-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .form-subtitle {
            font-size: 0.875rem;
            color: #64748b;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="form-container p-6">
            <!-- Header -->
            <div class="form-header">
                <div class="form-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h1 class="form-title">Edit Data Guru</h1>
                    <p class="form-subtitle">Perbarui informasi guru di sistem</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-red-500 mt-1">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
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
            
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-red-500 mt-1">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ session('error') }}</h3>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('teachers.update', $teacher) }}" id="editTeacherForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIP Field -->
                    <div>
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" id="nip" name="nip" required
                            class="form-input @error('nip') error-border @enderror"
                            value="{{ old('nip', $teacher->nip) }}">
                        @error('nip')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required
                            class="form-input @error('name') error-border @enderror"
                            value="{{ old('name', $teacher->name) }}">
                        @error('name')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" required
                            class="form-input @error('email') error-border @enderror"
                            value="{{ old('email', $teacher->user->email) }}">
                        @error('email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subjects Field -->
                    <div class="md:col-span-2">
                        <label for="subject_ids" class="form-label">Mata Pelajaran</label>
                        <select id="subject_ids" name="subject_ids[]" multiple
                            class="form-input @error('subject_ids') error-border @enderror">
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                    {{ in_array($subject->id, old('subject_ids', $teacher->subjects->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="form-hint">Tekan backspace untuk menghapus pilihan</p>
                        @error('subject_ids')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Classroom Field -->
                    <div class="md:col-span-2">
                        <label for="classroom" class="form-label">Wali Kelas (Opsional)</label>
                        <select id="classroom" name="classroom"
                            class="form-input @error('classroom') error-border @enderror">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}"
                                    {{ $classroom->id == old('classroom', $teacher->classroom_id) ? 'selected' : '' }}>
                                    {{ $classroom->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('classroom')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="md:col-span-2 pt-6 mt-6 flex justify-between border-t border-gray-100">
                        <a href="{{ route('teachers.index') }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <button type="submit"
                            class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Initialize Subjects Select2 (Multiple)
            $('#subject_ids').select2({
                placeholder: 'Pilih mata pelajaran...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                minimumResultsForSearch: 1,
                dropdownParent: $('#editTeacherForm'),
                language: {
                    noResults: function() {
                        return "Tidak ditemukan mata pelajaran";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    inputTooShort: function(args) {
                        return "Ketik minimal " + args.minimum + " karakter";
                    }
                },
                templateResult: function(data) {
                    if (!data.id) return data.text;
                    return $('<span>').addClass('text-sm').text(data.text);
                },
                templateSelection: function(data) {
                    return $('<span>').addClass('text-sm').text(data.text);
                }
            });

            // Initialize Classroom Select2 (Single)
            $('#classroom').select2({
                placeholder: 'Pilih kelas...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#editTeacherForm'),
                minimumResultsForSearch: 1,
                language: {
                    noResults: function() {
                        return "Tidak ditemukan kelas";
                    }
                }
            });

            // Set initial values
            let selectedSubjects = @json($teacher->subjects->pluck('id')->toArray());
            $('#subject_ids').val(selectedSubjects).trigger('change');

            // Form validation
            $('#editTeacherForm').on('submit', function(e) {
                let valid = true;
                
                // Validate required fields
                $('.form-input[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('error-border');
                        $(this).next('.error-message').remove();
                        $(this).after('<p class="error-message">Field ini wajib diisi</p>');
                        valid = false;
                    }
                });
                
                // Validate at least one subject selected
                if ($('#subject_ids').val() === null || $('#subject_ids').val().length === 0) {
                    $('#subject_ids').addClass('error-border');
                    $('#subject_ids').nextAll('.error-message').remove();
                    $('#subject_ids').after('<p class="error-message">Pilih minimal satu mata pelajaran</p>');
                    valid = false;
                }
                
                if (!valid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.error-border').first().offset().top - 100
                    }, 300);
                }
            });
            
            // Clear error when user starts typing/selecting
            $('.form-input').on('input change', function() {
                if ($(this).val()) {
                    $(this).removeClass('error-border');
                    $(this).nextAll('.error-message').remove();
                }
            });
            
            // Better keyboard navigation for Select2
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
        });
    </script>
</body>
</html>