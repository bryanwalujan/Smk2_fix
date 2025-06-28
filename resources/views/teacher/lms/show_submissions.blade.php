<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumpulan Tugas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head> 
<body class="bg-gray-50">
    @include('layouts.appguru')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Pengumpulan Tugas: {{ $assignment->title }}</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Assignment Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Informasi Tugas</h2>
            <p><strong>Kelas:</strong> {{ $assignment->classSession->classroom->full_name }}</p>
            <p><strong>Mata Pelajaran:</strong> {{ $assignment->classSession->subject_name }}</p>
            <p><strong>Deskripsi:</strong> {{ $assignment->description ?? '-' }}</p>
            <p><strong>Tenggat:</strong> {{ Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') }}</p>
        </div>

        <!-- Submissions -->
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Daftar Pengumpulan</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pengumpulan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($assignment->submissions as $submission)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->student->classroom->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($submission->submitted_at)->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($submission->file_path)
                                    <a href="{{ Storage::url($submission->file_path) }}" target="_blank"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-file"></i> Lihat File
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $submission->score ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form action="{{ route('teacher.lms.grade_submission', [$assignment, $submission]) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="number" name="score" value="{{ $submission->score ?? '' }}"
                                           placeholder="0-100" min="0" max="100" step="0.01"
                                           class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 ml-2">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada pengumpulan tugas untuk tugas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>