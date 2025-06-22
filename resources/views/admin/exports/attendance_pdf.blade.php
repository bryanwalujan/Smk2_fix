<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kehadiran</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #ff020a; }
    </style>
</head>
<body>
    <h1>Laporan Kehadiran Siswa</h1>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Waktu Masuk</th>
                <th>Waktu Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->student->nis ?? '-' }}</td>
                    <td>{{ $attendance->student->name ?? '-' }}</td>
                    <td>{{ $attendance->tanggal }}</td>
                    <td>{{ $attendance->waktu_masuk ?? '-' }}</td>
                    <td>{{ $attendance->waktu_pulang ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>