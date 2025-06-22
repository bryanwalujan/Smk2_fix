<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mata Pelajaran</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #ff020a; }
    </style>
</head>
<body>
    <h1>Laporan Daftar Mata Pelajaran</h1>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Nama Mata Pelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjects as $subject)
                <tr>
                    <td>{{ $subject->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>