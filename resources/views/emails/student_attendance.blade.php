<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Absensi Siswa</title>
</head>
<body>
    <h1>Notifikasi Absensi</h1>
    <p>Yth. Orang Tua/Wali dari {{ $studentName }},</p>
    <p>Kami ingin memberitahu bahwa anak Anda, <strong>{{ $studentName }}</strong>, telah melakukan absensi masuk di sekolah pada:</p>
    <ul>
        <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</li>
        <li><strong>Waktu:</strong> {{ $time }}</li>
    </ul>
    <p>Terima kasih atas perhatian Anda.</p>
    <p>Salam,<br>
    Tim Sekolah</p>
</body>
</html>