<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Kelas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { color: blue; margin-right: 10px; }
    </style>
</head>
<body>
    @include('layouts.navbar-admin')
    <h2>Detail Kelas: {{ $classroom->full_name }}</h2>
    <p>Jumlah Siswa: {{ $classroom->students->count() }}</p>
    <h3>Daftar Siswa</h3>
    @if ($classroom->students->isEmpty())
        <p>Tidak ada siswa di kelas ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classroom->students as $student)
                    <tr>
                        <td>{{ $student->nis }}</td>
                        <td>{{ $student->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <h3>Mata Pelajaran dan Guru</h3>
    @if ($classroom->teachers->isEmpty())
        <p>Tidak ada guru di kelas ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classroom->teachers as $teacher)
                    <tr>
                        <td>{{ $teacher->pivot->subject_name }}</td>
                        <td>{{ $teacher->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <h3>Jadwal</h3>
    <a href="{{ route('schedules.create', $classroom) }}">Tambah Jadwal</a>
    @if ($classroom->schedules->isEmpty())
        <p>Tidak ada jadwal di kelas ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classroom->schedules->sortBy(['day', 'start_time']) as $schedule)
                    <tr>
                        <td>{{ $schedule->day }}</td>
                        <td>{{ $schedule->subject_name }}</td>
                        <td>{{ $schedule->teacher->name }}</td>
                        <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <a href="{{ route('classrooms.index') }}">Kembali</a>
</body>
</html>