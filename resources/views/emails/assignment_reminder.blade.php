<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Tugas</title>
</head>
<body>
    <h1>Pengingat Tugas</h1>
    <p>Halo,</p>
    <p>Tugas berikut akan segera jatuh tempo:</p>
    <ul>
        <li><strong>Judul:</strong> {{ $assignment->title }}</li>
        <li><strong>Deskripsi:</strong> {{ $assignment->description }}</li>
        <li><strong>Deadline:</strong> {{ $assignment->deadline->format('d-m-Y H:i') }}</li>
    </ul>
    <p>Harap segera selesaikan tugas Anda sebelum tenggat waktu!</p>
    <p>Terima kasih,</p>
    <p>Sistem Manajemen Kelas</p>
</body>
</html>