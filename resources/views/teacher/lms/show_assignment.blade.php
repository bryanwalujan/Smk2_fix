@extends('layouts.appguru')

@section('title', 'Detail Tugas')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">{{ $assignment->title }}</h2>
            <a href="{{ route('teacher.lms.show_session', $classSession) }}" 
               class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Sesi
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-700">Deskripsi</h3>
            @if ($assignment->description)
                <div class="text-gray-600 prose max-w-none mt-2">
                    {!! nl2br(e($assignment->description)) !!}
                </div>
            @else
                <p class="text-gray-500 italic">Tidak ada deskripsi.</p>
            @endif
        </div>

        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-700">Tenggat Waktu</h3>
            <p class="text-gray-600 mt-2">
                {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('l, d F Y H:i') }}
                @if($assignment->deadline < now())
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Terlambat
                    </span>
                @endif
            </p>
        </div>

        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-700">Pengumpulan</h3>
            <p class="text-gray-600 mt-2">
                {{ $assignment->submissions->count() }} siswa telah mengumpulkan
                <a href="{{ route('teacher.lms.show_submissions', $assignment) }}" 
                   class="text-blue-600 hover:underline">Lihat pengumpulan</a>
            </p>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('teacher.lms.edit_assignment', [$classSession, $assignment]) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <form action="{{ route('teacher.lms.destroy_assignment', [$classSession, $assignment]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        onclick="return confirm('Yakin ingin menghapus tugas ini?')"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
@endsection