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
    @if(file_exists(public_path('qrcodes/teacher_' . auth()->user()->barcode . '.svg')))
        <img src="{{ asset('qrcodes/teacher_' . auth()->user()->barcode . '.svg') }}" alt="QR Code" class="qr-preview rounded border border-gray-200">
    @else
        <p>QR Code belum tersedia. Silakan hubungi administrator.</p>
    @endif
    <br>
    <a href="{{ route('teacher.lms.index') }}">Kelola LMS</a>
</body>
</html>