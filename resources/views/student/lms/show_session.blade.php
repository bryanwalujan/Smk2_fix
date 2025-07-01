
@extends('layouts.appstudent')

@section('title', 'Detail Sesi Kelas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Sesi: {{ $classSession->title ?? 'Tidak ada judul' }}</h2>
                    <div class="mt-2 space-y-1">
                        <p class="text-gray-600">
                            <span class="font-medium">Kelas:</span> {{ $classSession->classroom->full_name ?? 'Tidak ada kelas' }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Mata Pelajaran:</span> {{ $classSession->subject->name ?? 'Tidak ada mata pelajaran' }}
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium">Waktu:</span> 
                            {{ \Carbon\Carbon::parse($classSession->start_time)->translatedFormat('l, H:i') }} - 
                            {{ \Carbon\Carbon::parse($classSession->end_time)->format('H:i') }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('student.lms.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Kembali ke LMS
                </a>
            </div>
        </div>

        <!-- Materials Section -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Materi Pembelajaran</h3>
            
            @if ($classSession->materials->isEmpty())
                <p class="text-gray-500 italic">Tidak ada materi pembelajaran.</p>
            @else
                <div class="space-y-4">
                    @foreach ($classSession->materials as $material)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium text-gray-800">{{ $material->title }}</h4>
                            @if ($material->file_path)
                                <a href="{{ Storage::url($material->file_path) }}" target="_blank" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Unduh Materi
                                </a>
                            @endif
                        </div>
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
            
            @if ($classSession->assignments->isEmpty())
                <p class="text-gray-500 italic">Tidak ada tugas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenggat Waktu</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($classSession->assignments as $assignment)
                            @php
                                $submission = $assignment->submissions->first();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $assignment->title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                    {{ Str::limit($assignment->description, 100) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('l, d F Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($submission)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Sudah Dikumpulkan
                                        </span>
                                    @elseif ($assignment->deadline < now())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Tenggat Waktu Lewat
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Belum Dikumpulkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($submission && !is_null($submission->score))
                                        {{ $submission->score }}
                                    @else
                                        Belum Dinilai
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if (!$submission && $assignment->deadline >= now())
                                        <a href="{{ route('student.lms.create_submission', $assignment) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Kumpulkan
                                        </a>
                                    @elseif ($submission && $submission->file_path)
                                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
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