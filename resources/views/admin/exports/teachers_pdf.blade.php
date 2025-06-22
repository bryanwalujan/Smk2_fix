<!DOCTYPE html>
<html>
<head>
    <title>Laporan Guru</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #ff020a; }
    </style>
</head>
<body>
    <h1>Laporan Daftar Guru</h1>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>NIP</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Mata Pelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teachers as $teacher)
                <tr>
                    <td>{{ $teacher->nip }}</td>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->user->email ?? '-' }}</td>
                    <td>{{ $teacher->subjects->pluck('name')->implode(', ') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>