<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; text-align: center; }
        a { color: blue; margin-right: 10px; }
    </style>
</head>
<body>
    <h2>Selamat Datang, {{ auth()->user()->name }}</h2>
    <a href="{{ route('student.lms.index') }}">Akses LMS</a>
</body>
</html>