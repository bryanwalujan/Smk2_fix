@extends('layouts.appstudent')

@section('title', 'Dashboard LMS Siswa')

@section('content')
    <!-- Konten dashboard tetap sama seperti sebelumnya -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard Siswa</h1>
        <p class="text-gray-600">Materi dan tugas per mata pelajaran</p>
    </div>

    @if ($classSessions->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">Tidak ada materi atau tugas.</p>
        </div>
    @else
        @foreach ($classSessions as $subject => $sessions)
            <!-- ... (kode tabel tetap sama) ... -->
        @endforeach
    @endif
@endsection