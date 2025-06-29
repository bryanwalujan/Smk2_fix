@extends('layouts.appstudent')

@section('title', 'Dashboard LMS Siswa')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard LMS Siswa</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1 md:mt-2">Selamat datang di Learning Management System</p>
    </div>

    <!-- Materials and Assignments Section -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4 md:mb-6">Daftar Kelas</h2>
        
        @if ($classSessions->isEmpty())
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm md:text-base">
                <p>Belum ada kelas saat ini.</p>
            </div>
        @else
            @foreach ($classSessions as $subject => $sessions)
                <div class="mb-8 md:mb-10">
                    <h3 class="text-lg md:text-xl font-semibold text-gray-700 mb-3 md:mb-4 pb-2 border-b border-gray-200">{{ $subject }}</h3>
                    
                    <div class="overflow-x-auto">
                        <!-- Desktop Table -->
                        <table class="hidden md:table min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                   
                                    <th scope="col" class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                                    <th scope="col" class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                                    <th scope="col" class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                                    <th scope="col" class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th scope="col" class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($sessions as $session)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($session->day) }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->teacher->name ?? 'Tidak ada guru' }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->subject->name ?? 'Tidak ada mata pelajaran' }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('student.lms.show_session', $session) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Mobile Cards -->
                        <div class="md:hidden space-y-4">
                            @foreach ($sessions as $session)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $session->subject->name ?? 'Tidak ada mata pelajaran' }}</h4>
                                    
                                    </div>
                                    <a href="{{ route('student.lms.show_session', $session) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        Lihat
                                    </a>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <p class="text-gray-500">Hari</p>
                                        <p class="text-gray-900">{{ ucfirst($session->day) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Jam</p>
                                        <p class="text-gray-900">
                                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                        </p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-gray-500">Guru</p>
                                        <p class="text-gray-900">{{ $session->teacher->name ?? 'Tidak ada guru' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection