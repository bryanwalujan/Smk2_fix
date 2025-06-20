<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Sesi Kelas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { padding: 5px; width: 100%; max-width: 300px; }
        button { padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .error { color: red; }
        .logout-form { display: inline; }
    </style>
</head>
<body>
    <div style="margin-bottom: 20px;">
        <a href="{{ route('teacher.lms.index') }}">Kembali ke LMS</a>
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" style="background: none; border: none; color: blue; cursor: pointer; padding: 0;">Logout</button>
        </form>
    </div>
    <h2>Edit Sesi Kelas</h2>
    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('teacher.lms.update_session', $classSession) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="classroom_id">Kelas</label>
            <select name="classroom_id" id="classroom_id" required>
                @foreach ($subjects as $classroom_id => $subject_name)
                    <option value="{{ $classroom_id }}" {{ $classSession->classroom_id == $classroom_id ? 'selected' : '' }}>
                        {{ \App\Models\Classroom::find($classroom_id)->full_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="subject_name">Mata Pelajaran</label>
            <select name="subject_name" id="subject_name" required>
                @foreach ($subjects as $subject_name)
                    <option value="{{ $subject_name }}" {{ $classSession->subject_name == $subject_name ? 'selected' : '' }}>
                        {{ $subject_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="title">Judul Sesi</label>
            <input type="text" name="title" id="title" value="{{ old('title', $classSession->title) }}" required>
        </div>
        <div class="form-group">
            <label for="start_time">Waktu Mulai</label>
            <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', $classSession->start_time->format('Y-m-d\TH:i')) }}" required>
        </div>
        <div class="form-group">
            <label for="end_time">Waktu Selesai</label>
            <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', $classSession->end_time->format('Y-m-d\TH:i')) }}" required>
        </div>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>