@extends('layouts.appguru')

@section('title', 'Detail Materi')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800">{{ $material->title }}</h2>
            <a href="{{ route('teacher.lms.show_session', $classSession) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Sesi
            </a>
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
            <h3 class="text-lg font-medium text-gray-700">Konten</h3>
            @if ($material->content)
                <div class="text-gray-600 prose max-w-none mt-2">
                    {!! nl2br(e($material->content)) !!}
                </div>
            @else
                <p class="text-gray-500 italic">Tidak ada konten.</p>
            @endif
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-700">File</h3>
            @if ($material->file_path)
                <p class="text-gray-600 mt-2">
                    <a href="{{ Storage::url($material->file_path) }}" target="_blank" 
                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Lihat File
                    </a>
                </p>
            @else
                <p class="text-gray-500 italic">Tidak ada file.</p>
            @endif
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('teacher.lms.edit_material', [$classSession, $material]) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <form action="{{ route('teacher.lms.destroy_material', [$classSession, $material]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        onclick="return confirm('Yakin ingin menghapus materi ini?')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection