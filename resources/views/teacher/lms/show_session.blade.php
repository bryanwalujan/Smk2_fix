@extends('layouts.appguru')

@section('title', 'Detail Sesi {{ $classSession->subject->name }}')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Sesi: {{ $classSession->subject->name ?? 'Tidak ada mata pelajaran' }}</h2>
                    <div class="mt-2 space-y-1">
                        <p class="text-gray-600">
                            <span class="font-medium">Kelas:</span> {{ $classSession->classroom->full_name ?? 'Tidak ada kelas' }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Mata Pelajaran:</span> {{ $classSession->subject->name ?? 'Tidak ada mata pelajaran' }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Tanggal:</span> {{ \Carbon\Carbon::parse($classSession->date)->translatedFormat('l, d F Y') }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Waktu:</span> 
                            {{ \Carbon\Carbon::parse($classSession->start_time)->translatedFormat('H:i') }} - 
                            {{ \Carbon\Carbon::parse($classSession->end_time)->format('H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('teacher.lms.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kembali ke LMS
                    </a>
                    <a href="{{ route('teacher.lms.create_material', $classSession) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Tambah Materi
                    </a>
                    <a href="{{ route('teacher.lms.create_assignment', $classSession) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Tambah Tugas
                    </a>
                    <a href="{{ route('teacher.lms.show_attendance', $classSession) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kelola Absensi
                    </a>
                </div>
            </div>
        </div>

        <!-- Materials Section -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Materi Pembelajaran</h3>
            <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full mb-4 inline-block">
                Total: {{ $materials->count() }} Materi
            </div>
            @if ($materials->isEmpty())
                <p class="text-gray-500 italic">Tidak ada materi pembelajaran.</p>
            @else
                <div class="space-y-4">
                    @foreach ($materials as $material)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <h4 class="font-medium text-gray-800">{{ $material->title }}</h4>
                                <div class="flex space-x-2">
                                    <a href="{{ route('teacher.lms.show_material', [$classSession, $material]) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                    <a href="{{ route('teacher.lms.edit_material', [$classSession, $material]) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('teacher.lms.destroy_material', [$classSession, $material]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            @if ($material->file_path)
                                <a href="{{ Storage::url($material->file_path) }}" target="_blank" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2">
                                    Unduh Materi
                                </a>
                            @endif
                            @if ($material->content)
                                <div class="mt-2 text-gray-600 prose max-w-none">
                                    {!! nl2br(e($material->content)) !!}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Assignments Section -->
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Tugas</h3>
            <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full mb-4 inline-block">
                Total: {{ $assignments->count() }} Tugas
            </div>
            @if ($assignments->isEmpty())
                <p class="text-gray-500 italic">Tidak ada tugas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenggat Waktu</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($assignments as $assignment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $assignment->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                        {{ \Illuminate\Support\Str::limit($assignment->description, 100) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('l, d F Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('teacher.lms.show_assignment', [$classSession, $assignment]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-2">Lihat</a>
                                        <a href="{{ route('teacher.lms.edit_assignment', [$classSession, $assignment]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                        <a href="{{ route('teacher.lms.show_submissions', $assignment) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-2">Pengumpulan</a>
                                        <form action="{{ route('teacher.lms.destroy_assignment', [$classSession, $assignment]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection