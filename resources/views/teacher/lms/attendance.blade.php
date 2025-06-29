
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi Siswa - {{ $classSession->subject->name }} ({{ $classroom->name }})</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen p-4">
    <div class="container mx-auto">
        <h1 class="text-2xl font-semibold text-blue-600 mb-6">
            <i class="fas fa-clipboard-list mr-2"></i> Absensi Siswa - {{ $classSession->subject->name }} ({{ $classroom->name }})
        </h1>

        <!-- Tombol Kembali -->
        <div class="mb-6">
            <a href="{{ route('teacher.lms.show_session', $classSession) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Sesi
            </a>
        </div>

        <!-- Pesan Sukses/Error -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabel Absensi -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Daftar Siswa - Tanggal: {{ \Carbon\Carbon::parse($classSession->date)->translatedFormat('d F Y') }}</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 text-left">NIS</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Status Absensi</th>
                        <th class="px-4 py-2 text-left">Waktu Masuk</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $student->nis }}</td>
                            <td class="px-4 py-2">{{ $student->name }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $attendance = $student->attendances->first();
                                    $status = $attendance ? $attendance->status : 'Belum Absen';
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                    @if ($status === 'hadir')
                                        bg-green-100 text-green-800
                                    @elseif ($status === 'tidak_hadir')
                                        bg-red-100 text-red-800
                                    @elseif ($status === 'izin')
                                        bg-yellow-100 text-yellow-800
                                    @elseif ($status === 'sakit')
                                        bg-orange-100 text-orange-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                {{ $attendance ? \Carbon\Carbon::parse($attendance->waktu_masuk)->format('H:i') : '-' }}
                            </td>
                            <td class="px-4 py-2">
                                <form action="{{ route('teacher.lms.update_attendance', [$classSession, $student]) }}"
                                      method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="p-2 border rounded-lg">
                                        <option value="hadir" {{ $status === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="tidak_hadir" {{ $status === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                        <option value="izin" {{ $status === 'izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="sakit" {{ $status === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    </select>
                                    <button type="submit"
                                            class="ml-2 px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if ($students->isEmpty())
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-500">Tidak ada siswa di kelas ini.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
