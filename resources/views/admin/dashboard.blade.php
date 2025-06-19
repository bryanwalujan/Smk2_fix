<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    @include('layouts.navbar-admin')
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
            <div class="text-sm text-gray-500">
                <i class="fas fa-calendar-alt mr-2"></i>
                {{ now()->format('l, d F Y') }}
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                <a href="{{ route('students.index') }}" class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800">
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
                <a href="{{ route('teachers.index') }}" class="mt-4 inline-flex items-center text-green-600 hover:text-green-800">
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
                <a href="{{ route('classrooms.index') }}" class="mt-4 inline-flex items-center text-purple-600 hover:text-purple-800">
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
                <a href="{{ route('attendance.scan') }}" class="mt-4 inline-flex items-center text-yellow-600 hover:text-yellow-800">
                    Scan Presensi <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Aktivitas Terkini</h2>
                <div class="space-y-4">
                    @php
                        $recentActivities = App\Models\StudentAttendance::with('student')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start pb-4 border-b border-gray-100 last:border-0">
                            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $activity->student->name ?? 'Siswa Tidak Diketahui' }}
                                    <span class="text-sm font-normal text-gray-500">
                                        ({{ $activity->waktu_masuk }})
                                    </span>
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
                    <a href="{{ route('students.create') }}" class="flex items-center p-3 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                        <i class="fas fa-user-plus mr-3"></i>
                        <span>Tambah Siswa Baru</span>
                    </a>
                    <a href="{{ route('teachers.create') }}" class="flex items-center p-3 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition">
                        <i class="fas fa-chalkboard-teacher mr-3"></i>
                        <span>Tambah Guru Baru</span>
                    </a>
                    <a href="{{ route('classrooms.create') }}" class="flex items-center p-3 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition">
                        <i class="fas fa-door-open mr-3"></i>
                        <span>Buat Kelas Baru</span>
                    </a>
                    <a href="{{ route('attendance.scan') }}" class="flex items-center p-3 rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition">
                        <i class="fas fa-qrcode mr-3"></i>
                        <span>Scan Presensi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>