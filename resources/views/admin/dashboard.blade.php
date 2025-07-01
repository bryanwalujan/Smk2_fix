<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
    @include('layouts.navbar-admin')
    
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Header with Breadcrumb -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 md:text-3xl">Dashboard Admin</h1>
                    <p class="text-sm text-gray-600 mt-1">Ringkasan aktivitas dan statistik sistem</p>
                </div>
                <div class="mt-2 md:mt-0">
                    <div class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <i class="fas/fa-calendar-day text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ now()->format('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <section class="mb-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('errors'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada data yang diimpor:</h3>
                            <ul class="mt-2 text-sm text-red-700 space-y-1">
                                @foreach (session('errors') as $error)
                                    <li class="flex items-start">
                                        <span class="mr-2">•</span>
                                        <span>{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <!-- Stats Cards -->
        <section class="mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Siswa -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-blue-50 text-blue-600 mr-4">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Siswa</p>
                                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ App\Models\Student::count() }}</h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('students.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 inline-flex items-center">
                                Kelola Siswa <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Total Guru -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-green-50 text-green-600 mr-4">
                                <i class="fas fa-chalkboard-teacher text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Guru</p>
                                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ App\Models\Teacher::count() }}</h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('teachers.index') }}" class="text-sm font-medium text-green-600 hover:text-green-700 inline-flex items-center">
                                Kelola Guru <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Total Kelas -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-purple-50 text-purple-600 mr-4">
                                <i class="fas fa-door-open text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kelas</p>
                                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ App\Models\Classroom::count() }}</h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('classrooms.index') }}" class="text-sm font-medium text-purple-600 hover:text-purple-700 inline-flex items-center">
                                Kelola Kelas <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Presensi Siswa Hari Ini -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-amber-50 text-amber-600 mr-4">
                                <i class="fas fa-clipboard-check text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Presensi Hari Ini</p>
                                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ App\Models\StudentAttendance::whereDate('tanggal', now()->toDateString())->count() }}</h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('attendance.scan') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700 inline-flex items-center">
                                Scan Presensi <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-8 " x-data="{ activeTab: 'import' }">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Manajemen Data</h2>
                    <p class="text-sm text-gray-600 mt-1">Impor atau ekspor data dalam format Excel/PDF</p>
                </div>
                
                <!-- Tabs -->
                <div class="border-b border-gray-200 p-5">
                    <nav class="-mb-px flex space-x-8">
                        <button 
                            @click="activeTab = 'import'" 
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'import', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'import' }" 
                            class="border-b-2 px-1 py-3 text-sm font-medium">
                            Impor Data
                        </button>
                        <button 
                            @click="activeTab = 'export'" 
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'export', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'export' }" 
                            class="border-b-2 px-1 py-3 text-sm font-medium">
                            Ekspor Data
                        </button>
                    </nav>
                </div>
                
                <!-- Tab Content -->
                <div class="mt-6 p-5">
                    <!-- Import Tab -->
                    <div x-show="activeTab === 'import'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Import Students -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Impor Data Siswa</h3>
                                    <p class="text-sm text-gray-500 mt-1">Unggah file Excel berisi data siswa</p>
                                    
                                    <div class="mt-4 space-y-3">
                                        <a href="{{ route('admin.export.students.template') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-download mr-2"></i> Unduh Template
                                        </a>
                                        
                                        <form action="{{ route('admin.import.students') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                            @csrf
                                            <div class="flex-1">
                                                <label class="sr-only">Pilih file</label>
                                                <input type="file" name="file" accept=".xlsx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            </div>
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                                <i class="fas fa-upload mr-1"></i> Unggah
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Import Teachers -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Impor Data Guru</h3>
                                    <p class="text-sm text-gray-500 mt-1">Unggah file Excel berisi data guru</p>
                                    
                                    <div class="mt-4 space-y-3">
                                        <a href="{{ route('admin.export.teachers.template') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-download mr-2"></i> Unduh Template
                                        </a>
                                        
                                        <form action="{{ route('admin.import.teachers') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                            @csrf
                                            <div class="flex-1">
                                                <label class="sr-only">Pilih file</label>
                                                <input type="file" name="file" accept=".xlsx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                            </div>
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                                <i class="fas fa-upload mr-1"></i> Unggah
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Import Classrooms -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Impor Data Kelas</h3>
                                    <p class="text-sm text-gray-500 mt-1">Unggah file Excel berisi data kelas</p>
                                    
                                    <div class="mt-4 space-y-3">
                                        <a href="{{ route('admin.export.classrooms.template') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-download mr-2"></i> Unduh Template
                                        </a>
                                        
                                        <form action="{{ route('admin.import.classrooms') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                            @csrf
                                            <div class="flex-1">
                                                <label class="sr-only">Pilih file</label>
                                                <input type="file" name="file" accept=".xlsx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                            </div>
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700">
                                                <i class="fas fa-upload mr-1"></i> Unggah
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Import Subjects -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-red-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Impor Data Mata Pelajaran</h3>
                                    <p class="text-sm text-gray-500 mt-1">Unggah file Excel berisi data mata pelajaran</p>
                                    
                                    <div class="mt-4 space-y-3">
                                        <a href="{{ route('admin.export.subjects.template') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-download mr-2"></i> Unduh Template
                                        </a>
                                        
                                        <form action="{{ route('admin.import.subjects') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                            @csrf
                                            <div class="flex-1">
                                                <label class="sr-only">Pilih file</label>
                                                <input type="file" name="file" accept=".xlsx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                            </div>
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                                                <i class="fas fa-upload mr-1"></i> Unggah
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Tab -->
                    <div x-show="activeTab === 'export'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Export Students -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Ekspor Data Siswa</h3>
                                    <p class="text-sm text-gray-500 mt-1">Ekspor data siswa dalam format Excel</p>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.export.students.excel') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                            <i class="fas fa-file-excel mr-1"></i> Ekspor Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Export Teachers -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Ekspor Data Guru</h3>
                                    <p class="text-sm text-gray-500 mt-1">Ekspor data guru dalam format Excel</p>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.export.teachers.excel') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                            <i class="fas fa-file-excel mr-1"></i> Ekspor Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Export Classrooms -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Ekspor Data Kelas</h3>
                                    <p class="text-sm text-gray-500 mt-1">Ekspor data kelas dalam format Excel</p>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.export.classrooms.excel') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700">
                                            <i class="fas fa-file-excel mr-1"></i> Ekspor Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Export Attendance -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-amber-300 transition-colors">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-medium text-gray-900">Ekspor Data Absensi</h3>
                                    <p class="text-sm text-gray-500 mt-1">Ekspor data absensi berdasarkan periode</p>
                                    
                                    <form action="{{ route('admin.export.attendance.excel') }}" method="GET" class="mt-4 space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label for="start_date_excel" class="block text-xs font-medium text-gray-700">Mulai</label>
                                                <input type="date" id="start_date_excel" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5">
                                            </div>
                                            <div>
                                                <label for="end_date_excel" class="block text-xs font-medium text-gray-700">Selesai</label>
                                                <input type="date" id="end_date_excel" name="end_date" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5">
                                            </div>
                                            <div>
                                                <label for="type_excel" class="block text-xs font-medium text-gray-700">Tipe</label>
                                                <select id="type_excel" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5">
                                                    <option value="all">Semua</option>
                                                    <option value="student">Siswa</option>
                                                    <option value="teacher">Guru</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                                <i class="fas fa-file-excel mr-1"></i> Ekspor Excel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
            <!-- Recent Activities -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Aktivitas Terkini</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @php
                            $recentActivities = App\Models\StudentAttendance::with('student')->orderBy('created_at', 'desc')->limit(6)->get();
                        @endphp
                        @forelse($recentActivities as $activity)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 pt-1">
                                        <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $activity->student->name ?? 'Siswa Tidak Diketahui' }}
                                            </p>
                                            <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            @if($activity->waktu_pulang)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-sign-out-alt mr-1"></i> Check-out {{ $activity->waktu_pulang }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-sign-in-alt mr-1"></i> Check-in {{ $activity->waktu_masuk }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">
                                Tidak ada aktivitas terakhir
                            </div>
                        @endforelse
                    </div>
                    <div class="px-5 py-3 bg-gray-50 text-right">
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 inline-flex items-center">
                            Lihat Semua Aktivitas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Aksi Cepat</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <a href="{{ route('students.create') }}" class="group flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Tambah Siswa Baru</p>
                                <p class="text-xs text-gray-500">Input data siswa baru</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('teachers.create') }}" class="group flex items-center p-3 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Tambah Guru Baru</p>
                                <p class="text-xs text-gray-500">Input data guru baru</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('classrooms.create') }}" class="group flex items-center p-3 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Buat Kelas Baru</p>
                                <p class="text-xs text-gray-500">Tambah kelas baru</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('subjects.create') }}" class="group flex items-center p-3 rounded-lg border border-gray-200 hover:border-red-300 hover:bg-red-50 transition-colors">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Tambah Mata Pelajaran</p>
                                <p class="text-xs text-gray-500">Input mata pelajaran baru</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('attendance.scan') }}" class="group flex items-center p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Scan Presensi</p>
                                <p class="text-xs text-gray-500">Scan QR code presensi</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>