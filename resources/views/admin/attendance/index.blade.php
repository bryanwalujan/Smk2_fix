<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif
        @if (isset($isHoliday) && $isHoliday)
            <div class="mb-6 p-4 bg-yellow-100 text-yellow-700 border-l-4 border-yellow-500 rounded-lg flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                {{ $holidayMessage }}
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Absensi</h1>
                <p class="text-gray-600">Daftar absensi siswa dan guru</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('attendance.create') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Absensi</span>
                </a>
                <a href="{{ route('attendance.scan') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-qrcode"></i>
                    <span>Scan Barcode</span>
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
            <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-col md:flex-row gap-4 mb-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" id="date" name="date" value="{{ $date }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipe</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="student" {{ $type === 'student' ? 'selected' : '' }}>Siswa</option>
                        <option value="teacher" {{ $type === 'teacher' ? 'selected' : '' }}>Guru</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filter
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Masuk</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pulang</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($attendances as $attendance)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->user_name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->user_type === 'student' ? 'Siswa' : 'Guru' }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ Carbon\Carbon::parse($attendance->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->waktu_masuk }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $attendance->waktu_pulang ?? '-' }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ ucfirst($attendance->status) }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ ucfirst($attendance->metode_absen) }}</td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <a href="{{ route('attendance.edit', ['id' => $attendance->id, 'type' => $attendance->user_type]) }}"
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                                       title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 px-6 text-center text-gray-500">
                                    Tidak ada data absensi tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>
</body>
</html>