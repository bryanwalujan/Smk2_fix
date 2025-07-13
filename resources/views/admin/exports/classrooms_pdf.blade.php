<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kelas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #ff020a; }
    </style>
</head>
<body>
    <h1>Laporan Daftar Kelas</h1>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Tingkat</th>
                <th>Jurusan</th>
                <th>Kode Kelas</th>
                <th>Nama Lengkap</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($classrooms as $classroom)
                <tr>
                    <td>{{ $classroom->level }}</td>
                    <td>{{ $classroom->major }}</td>
                    <td>{{ $classroom->class_code }}</td>
                    <td>{{ $classroom->full_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>