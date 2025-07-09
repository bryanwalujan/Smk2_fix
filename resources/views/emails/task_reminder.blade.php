<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Tugas</title>
</head>
<body>
    <h1>Pengingat Tugas: {{ $assignment->title }}</h1>
    <p>Halo,</p>
    <p>Tugas <strong>{{ $assignment->title }}</strong> akan berakhir pada:</p>
    <p><strong>{{ $assignment->deadline->format('d M Y H:i') }}</strong></p>
    <p>Deskripsi: {{ $assignment->description }}</p>
    <p>Silakan serahkan tugas Anda sebelum batas waktu.</p>
    <p>Terima kasih,</p>
    <p>Tim Sekolah</p>
</body>
</html>