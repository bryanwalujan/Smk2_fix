@extends('layouts.appguru')

@section('title', 'Edit Tugas')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit Tugas</h2>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('teacher.lms.update_assignment', [$classSession, $assignment]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Tugas</label>
                <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('title')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $assignment->description) }}</textarea>
                @error('description')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="deadline" class="block text-sm font-medium text-gray-700">Tenggat Waktu</label>
                <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline', \Carbon\Carbon::parse($assignment->deadline)->format('Y-m-d\TH:i')) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('deadline')
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