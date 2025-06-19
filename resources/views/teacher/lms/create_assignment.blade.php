<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Tambah Tugas</title>
         <style>
             body { font-family: Arial, sans-serif; margin: 50px; }
             .form-group { margin-bottom: 15px; }
             input, textarea, select { padding: 5px; width: 100%; }
             button { padding: 10px; background-color: #4CAF50; color: white; border: none; }
             .error { color: red; }
         </style>
     </head>
     <body>
         <h2>Tambah Tugas untuk Sesi: {{ $classSession->title }}</h2>
         @if ($errors->any())
             <div class="error">
                 <ul>
                     @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                     @endforeach
                 </ul>
             </div>
         @endif
         <form method="POST" action="{{ route('teacher.lms.store_assignment', $classSession) }}">
             @csrf
             <div class="form-group">
                 <label>Judul Tugas</label>
                 <input type="text" name="title" value="{{ old('title') }}" required>
             </div>
             <div class="form-group">
                 <label>Deskripsi</label>
                 <textarea name="description" rows="5">{{ old('description') }}</textarea>
             </div>
             <div class="form-group">
                 <label>Tenggat Waktu</label>
                 <input type="datetime-local" name="deadline" value="{{ old('deadline') }}" required>
             </div>
             <button type="submit">Simpan Tugas</button>
         </form>
         <a href="{{ route('teacher.lms.show_session', $classSession) }}">Kembali</a>
     </body>
     </html>