@extends('layouts.appguru')

@section('title', 'Edit Materi')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit Materi</h2>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('teacher.lms.update_material', [$classSession, $material]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Materi</label>
                <input type="text" name="title" id="title" value="{{ old('title', $material->title) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('title')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700">Konten</label>
                <textarea name="content" id="content" rows="6"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('content', $material->content) }}</textarea>
                @error('content')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700">File (PDF, Doc, Gambar, Video)</label>
                <input type="file" name="file" id="file" class="mt-1 block w-full">
                @if ($material->file_path)
                    <p class="text-sm text-gray-600 mt-1">File saat ini: <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="text-blue-600">Lihat file</a></p>
                @endif
                @error('file')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-end">
                <a href="{{ route('teacher.lms.show_session', $classSession) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md mr-2">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Simpan</button>
            </div>
        </form>
    </div>
@endsection