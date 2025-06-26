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

<body class="bg-gray-50">
    @include('layouts.navbar-admin')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
            <div class="text-sm text-gray-500">
                <i class="fas fa-calendar-alt mr-2"></i>
                {{ now()->format('l, d F Y') }}
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session('errors'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada data yang diimpor:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach (session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Siswa</p>
                        <h3 class="text-2xl font-bold">{{ App\Models\Student::count() }}</h3>
                    </div>
                </div>
                <a href="{{ route('students.index') }}"
                    class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800">
                    Lihat detail <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-chalkboard-teacher text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Guru</p>
                        <h3 class="text-2xl font-bold">{{ App\Models\Teacher::count() }}</h3>
                    </div>
                </div>
                <a href="{{ route('teachers.index') }}"
                    class="mt-4 inline-flex items-center text-green-600 hover:text-green-800">
                    Lihat detail <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-door-open text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Kelas</p>
                        <h3 class="text-2xl font-bold">{{ App\Models\Classroom::count() }}</h3>
                    </div>
                </div>
                <a href="{{ route('classrooms.index') }}"
                    class="mt-4 inline-flex items-center text-purple-600 hover:text-purple-800">
                    Lihat detail <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-book text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Mata Pelajaran</p>
                        <h3 class="text-2xl font-bold">{{ App\Models\Subject::count() }}</h3>
                    </div>
                </div>
                <a href="{{ route('subjects.index') }}"
                    class="mt-4 inline-flex items-center text-red-600 hover:text-red-800">
                    Lihat detail <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Presensi Siswa Hari Ini</p>
                        <h3 class="text-2xl font-bold">
                            {{ App\Models\StudentAttendance::whereDate('tanggal', now()->toDateString())->count() }}
                        </h3>
                    </div>
                </div>
                <a href="{{ route('attendance.scan') }}"
                    class="mt-4 inline-flex items-center text-yellow-600 hover:text-yellow-800">
                    Scan Presensi <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Import/Export Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Impor/Ekspor Data</h2>
            <p class="text-sm text-gray-600 mb-6">Unduh template Excel, isi data sesuai petunjuk, lalu unggah file untuk
                menambahkan data baru. Ekspor data absensi dalam format Excel atau PDF.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Import Students -->
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Impor Data Siswa</h3>
                    <p class="text-sm text-gray-600 mb-4">Unduh template, isi data siswa, lalu unggah file di bawah ini.
                    </p>
                    <a href="{{ route('admin.export.students.template') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mb-4">
                        <i class="fas fa-download mr-2"></i> Unduh Template Siswa
                    </a>
                    <form action="{{ route('admin.import.students') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <input type="file" name="file" accept=".xlsx"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-upload mr-2"></i> Unggah File
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Import Teachers -->
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Impor Data Guru</h3>
                    <p class="text-sm text-gray-600 mb-4">Unduh template, isi data guru, lalu unggah file di bawah ini.
                    </p>
                    <a href="{{ route('admin.export.teachers.template') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mb-4">
                        <i class="fas fa-download mr-2"></i> Unduh Template Guru
                    </a>
                    <form action="{{ route('admin.import.teachers') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <input type="file" name="file" accept=".xlsx"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file-bg-indigo-100">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-upload mr-2"></i> Unggah File
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Import Classrooms -->
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Impor Data Kelas</h3>
                    <p class="text-sm text-gray-600 mb-4">Unduh template, isi data kelas, lalu unggah file di bawah ini.
                    </p>
                    <a href="{{ route('admin.export.classrooms.template') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mb-4">
                        <i class="fas fa-download mr-2"></i> Unduh Template Kelas
                    </a>
                    <form action="{{ route('admin.import.classrooms') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <input type="file" name="file" accept=".xlsx"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file-bg-indigo-100">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-upload mr-2"></i> Unggah File
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Import Subjects -->
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Impor Data Mata Pelajaran</h3>
                    <p class="text-sm text-gray-600 mb-4">Unduh template, isi data mata pelajaran, lalu unggah file di
                        sini.</p>
                    <a href="{{ route('admin.export.subjects.template') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mb-4">
                        <i class="fas fa-download mr-2"></i> Unduh Template Mata Pelajaran
                    </a>
                    <form action="{{ route('admin.import.subjects') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <input type="file" name="file" accept=".xlsx"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file-bg-indigo-100">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-upload mr-2"></i> Unggah File
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Export Attendance -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Ekspor Data Absensi</h3>
                    <p class="text-sm text-gray-600 mb-4">Pilih rentang tanggal dan tipe pengguna untuk mengekspor data
                        absensi dalam format Excel atau PDF.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Export Attendance Excel -->
                        <form action="{{ route('admin.export.attendance.excel') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="start_date_excel"
                                        class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                    <input type="date" id="start_date_excel" name="start_date"
                                        value="{{ now()->startOfMonth()->toDateString() }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label for="end_date_excel" class="block text-sm font-medium text-gray-700">Tanggal
                                        Selesai</label>
                                    <input type="date" id="end_date_excel" name="end_date"
                                        value="{{ now()->toDateString() }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label for="type_excel" class="block text-sm font-medium text-gray-700">Tipe
                                        Pengguna</label>
                                    <select id="type_excel" name="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="all">Semua</option>
                                        <option value="student">Siswa</option>
                                        <option value="teacher">Guru</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                                <i class="fas fa-file-excel mr-2"></i> Ekspor Excel
                            </button>
                        </form>
                        <!-- Export Attendance PDF -->
                        <form action="{{ route('admin.export.attendance.pdf') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="start_date_pdf" class="block text-sm font-medium text-gray-700">Tanggal
                                        Mulai</label>
                                    <input type="date" id="start_date_pdf" name="start_date"
                                        value="{{ now()->startOfMonth()->toDateString() }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label for="end_date_pdf" class="block text-sm font-medium text-gray-700">Tanggal
                                        Selesai</label>
                                    <input type="date" id="end_date_pdf" name="end_date"
                                        value="{{ now()->toDateString() }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label for="type_pdf" class="block text-sm font-medium text-gray-700">Tipe
                                        Pengguna</label>
                                    <select id="type_pdf" name="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="all">Semua</option>
                                        <option value="student">Siswa</option>
                                        <option value="teacher">Guru</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 transition">
                                <i class="fas fa-file-pdf mr-2"></i> Ekspor PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Export Data Links -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Ekspor Data Lain</h3>
                <p class="text-sm text-gray-600 mb-4">Ekspor data siswa, guru, kelas, atau mata pelajaran dalam format
                    Excel atau PDF.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.export.students.excel') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md text-sm font-medium hover:bg-indigo-100 transition">
                        <i class="fas fa-file-excel mr-2"></i> Siswa (Excel)
                    </a>
                    <a href="{{ route('admin.export.students.pdf') }}"
                        class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-md text-sm font-medium hover:bg-red-100 transition">
                        <i class="fas fa-file-pdf mr-2"></i> Siswa (PDF)
                    </a>
                    <a href="{{ route('admin.export.teachers.excel') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md text-sm font-medium hover:bg-indigo-100 transition">
                        <i class="fas fa-file-excel mr-2"></i> Guru (Excel)
                    </a>
                    <a href="{{ route('admin.export.teachers.pdf') }}"
                        class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-md text-sm font-medium hover:bg-red-100 transition">
                        <i class="fas fa-file-pdf mr-2"></i> Guru (PDF)
                    </a>
                    <a href="{{ route('admin.export.classrooms.excel') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md text-sm font-medium hover:bg-indigo-100 transition">
                        <i class="fas fa-file-excel mr-2"></i> Kelas (Excel)
                    </a>
                    <a href="{{ route('admin.export.classrooms.pdf') }}"
                        class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-md text-sm font-medium hover:bg-red-100 transition">
                        <i class="fas fa-file-pdf mr-2"></i> Kelas (PDF)
                    </a>
                    <a href="{{ route('admin.export.subjects.excel') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md text-sm font-medium hover:bg-indigo-100 transition">
                        <i class="fas fa-file-excel mr-2"></i> Mata Pelajaran (Excel)
                    </a>
                    <a href="{{ route('admin.export.subjects.pdf') }}"
                        class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-md text-sm font-medium hover:bg-red-100 transition">
                        <i class="fas fa-file-pdf mr-2"></i> Mata Pelajaran (PDF)
                    </a>
                </div>
            </div>
        </div>

        <!-- Role and Permission Management -->
        @can('manage_roles')
            <div class="bg-white rounded-lg shadow p-6 mb-8" x-data="permissionManager()">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Manajemen Izin</h2>
                <p class="text-sm text-gray-600 mb-6">Kelola izin untuk role teacher dan student. Klik tombol izin untuk
                    menambah atau menghapus izin.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach (['teacher', 'student'] as $roleName)
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-4">{{ ucfirst($roleName) }} Permissions</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($permissions as $permission)
                                    <button type="button"
                                        x-bind:class="isPermissionActive('{{ $roleName }}', '{{ $permission->name }}') ? 'bg-green-500 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                                        class="px-3 py-1 rounded-md text-sm font-medium hover:bg-green-400 transition"
                                        x-on:click="togglePermission('{{ $roleName }}', '{{ $permission->name }}')">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <script>
                    function permissionManager() {
                        return {
                            permissions: @json($roles->mapWithKeys(function ($role) {
                                return [$role->name => $role->permissions->pluck('name')->toArray()];
                            })),
                            isPermissionActive(role, permission) {
                                return this.permissions[role].includes(permission);
                            },
                            async togglePermission(role, permission) {
                                try {
                                    const response = await fetch('{{ route('admin.permissions.toggle') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        },
                                        body: JSON.stringify({ role, permission }),
                                    });

                                    console.log('Response Status:', response.status);
                                    console.log('Response Headers:', Object.fromEntries(response.headers));

                                    const contentType = response.headers.get('content-type');
                                    if (!contentType || !contentType.includes('application/json')) {
                                        const text = await response.text();
                                        console.error('Non-JSON response:', text.substring(0, 500)); // Log first 500 chars
                                        throw new Error(`Server returned non-JSON response. Status: ${response.status}`);
                                    }

                                    const data = await response.json();
                                    if (data.success) {
                                        if (this.permissions[role].includes(permission)) {
                                            this.permissions[role] = this.permissions[role].filter(p => p !== permission);
                                        } else {
                                            this.permissions[role].push(permission);
                                        }
                                        alert(data.message);
                                    } else {
                                        alert(data.message);
                                    }
                                } catch (error) {
                                    console.error('Toggle permission error:', error);
                                    alert('Gagal memperbarui izin: ' + error.message);
                                }
                            }
                        }
                    }
                </script>
            </div>
        @endcan

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Aktivitas Terkini</h2>
                <div class="space-y-4">
                    @php
                        $recentActivities = App\Models\StudentAttendance::with('student')->orderBy('created_at', 'desc')->limit(5)->get();
                    @endphp
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start pb-4 border-b border-gray-100 last:border-0">
                            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $activity->student->name ?? 'Siswa Tidak Diketahui' }}
                                    <span class="text-sm font-normal text-gray-500">({{ $activity->waktu_masuk }})</span>
                                </p>
                                <p class="text-sm text-gray-600">
                                    @if($activity->waktu_pulang)
                                        Check-out pada {{ $activity->waktu_pulang }}
                                    @else
                                        Check-in hari ini
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Tidak ada aktivitas terakhir</p>
                    @endforelse
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Aksi Cepat</h2>
                <div class="space-y-3">
                    <a href="{{ route('students.create') }}"
                        class="flex items-center p-3 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                        <i class="fas fa-user-plus mr-3"></i> Tambah Siswa Baru
                    </a>
                    <a href="{{ route('teachers.create') }}"
                        class="flex items-center p-3 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition">
                        <i class="fas fa-chalkboard-teacher mr-3"></i> Tambah Guru Baru
                    </a>
                    <a href="{{ route('classrooms.create') }}"
                        class="flex items-center p-3 rounded-lg bg-purple-50 text-purple-700 hover:bg-green-100 transition">
                        <i class="fas fa-door-open mr-3"></i> Buat Kelas Baru
                    </a>
                    <a href="{{ route('subjects.create') }}"
                        class="flex items-center p-3 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">
                        <i class="fas fa-book mr-3"></i> Tambah Mata Pelajaran
                    </a>
                    <a href="{{ route('attendance.scan') }}"
                        class="flex items-center p-3 rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition">
                        <i class="fas fa-qrcode mr-3"></i> Scan Presensi
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>