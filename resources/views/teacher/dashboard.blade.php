<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Guru</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; text-align: center; }
        img { margin: 20px; }
        a { color: blue; margin-right: 10px; }
    </style>
</head>
<body>
    <h2>Selamat Datang, {{ auth()->user()->name }}</h2>
    <h3>QR Code Absensi Anda</h3>
    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate(auth()->user()->barcode) !!}
    <br>
    <a href="{{ route('teacher.lms.index') }}">Kelola LMS</a>
</body>
</html>