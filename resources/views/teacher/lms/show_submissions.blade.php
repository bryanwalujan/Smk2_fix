@extends('layouts.appguru')

@section('title', 'Pengumpulan Tugas')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Pengumpulan Tugas: {{ $assignment->title }}</h2>
            @if($classSession)
                <a href="{{ route('teacher.lms.show_session', $classSession) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Sesi
                </a>
            @else
                <a href="{{ route('teacher.lms.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Dashboard
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-700">Daftar Pengumpulan</h3>
            <div class="overflow-x-auto mt-2">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pengumpulan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($assignment->submissions as $submission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->nis }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->classroom->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('d F Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($submission->file_path)
                                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" 
                                           class="text-indigo-600 hover:text-indigo-900">Unduh</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $submission->notes ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $submission->score ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <form action="{{ route('teacher.lms.grade_submission', [$assignment, $submission]) }}" 
                                          method="POST" class="inline-flex items-center space-x-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="score" value="{{ $submission->score ?? '' }}" 
                                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                               min="0" max="100" placeholder="0-100">
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada pengumpulan tugas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection