<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard LMS Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        h3 { margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { color: blue; margin-right: 10px; }
        .logout-form { display: inline; }
    </style>
</head>
<body>
    <h2>Dashboard LMS Siswa</h2>
    <div style="margin-bottom: 20px;">
        <a href="{{ route('student.dashboard') }}">Kembali ke Dashboard</a>
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" style="background: none; border: none; color: blue; cursor: pointer; padding: 0;">Logout</button>
        </form>
    </div>
    <h3>Materi dan Tugas per Mata Pelajaran</h3>
    @if ($classSessions->isEmpty())
        <p>Tidak ada materi atau tugas.</p>
    @else
        @foreach ($classSessions as $subject => $sessions)
            <h3>{{ $subject }}</h3>
            <table>
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Hari</th> 
                        <th>Jam</th> 
                        <th>Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                        <tr>
                            <td>{{ $session->classroom->full_name }}</td>
                            <td>{{ ucfirst($session->day_of_week) }}</td> 
                            <td>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</td> <!-- Tampilkan jam -->
                            <td>{{ $session->teacher->name ?? 'Tidak ada guru' }}</td>
                            <td>{{ $session->subject_name }}</td>
                            <td>
                                <a href="{{ route('student.lms.show_session', $session) }}">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>