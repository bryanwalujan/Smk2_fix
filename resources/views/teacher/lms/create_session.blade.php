<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Sesi Kelas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin-bottom: 15px; }
        input, select { padding: 5px; width: 100%; }
        button { padding: 10px; background-color: #4CAF50; color: white; border: none; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Tambah Sesi Kelas</h2>
    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('teacher.lms.store_session') }}">
        @csrf
        <div class="form-group">
            <label>Kelas dan Mata Pelajaran</label>
            <select name="classroom_id" id="classroom_id" required>
                <option value="">Pilih Kelas dan Mata Pelajaran</option>
                @foreach ($subjects as $classroom_id => $subject_name)
                    <option value="{{ $classroom_id }}">{{ \App\Models\Classroom::find($classroom_id)->full_name }} - {{ $subject_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Judul Sesi</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>Waktu Mulai</label>
            <input type="datetime-local" name="start_time" required>
        </div>
        <div class="form-group">
            <label>Waktu Selesai</label>
            <input type="datetime-local" name="end_time" required>
        </div>
        <input type="hidden" name="subject_name" id="subject_name">
        <button type="submit">Simpan</button>
    </form>
    <a href="{{ route('teacher.lms.index') }}">Kembali</a>
    <script>
        document.getElementById('classroom_id').addEventListener('change', function() {
            const subjects = @json($subjects);
            document.getElementById('subject_name').value = subjects[this.value] || '';
        });
    </script>
</body>
</html>