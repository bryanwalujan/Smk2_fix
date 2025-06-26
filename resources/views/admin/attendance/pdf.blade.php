<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
        .period { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Laporan Absensi</h1>
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        <br>
        Tipe: {{ $type === 'all' ? 'Semua' : ($type === 'student' ? 'Siswa' : 'Guru') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Waktu Masuk</th>
                <th>Waktu Pulang</th>
                <th>Status</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user_name }}</td>
                    <td>{{ $attendance->user_type === 'student' ? 'Siswa' : 'Guru' }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $attendance->waktu_masuk }}</td>
                    <td>{{ $attendance->waktu_pulang ?? '-' }}</td>
                    <td>{{ ucfirst($attendance->status) }}</td>
                    <td>{{ ucfirst($attendance->metode_absen) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data absensi tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>