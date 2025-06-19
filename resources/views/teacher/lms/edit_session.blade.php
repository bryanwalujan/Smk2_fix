<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Edit Sesi Kelas</title>
         <style>
             body { font-family: Arial, sans-serif; margin: 50px; }
             .form-group { margin-bottom: 15px; }
             input, select { padding: 5px; width: 100%; }
             button { padding: 10px; background-color: #4CAF50; color: white; border: none; }
             .error { color: red; }
         </style>
     </head>
     <body>
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
                 <label>Kelas dan Mata Pelajaran</label>
                 <select name="classroom_id" id="classroom_id" required>
                     <option value="">Pilih Kelas dan Mata Pelajaran</option>
                     @foreach ($subjects as $classroom_id => $subject_name)
                         <option value="{{ $classroom_id }}" {{ $classSession->classroom_id == $classroom_id && $classSession->subject_name == $subject_name ? 'selected' : '' }}>
                             {{ \App\Models\Classroom::find($classroom_id)->full_name }} - {{ $subject_name }}
                         </option>
                     @endforeach
                 </select>
             </div>
             <div class="form-group">
                 <label>Judul Sesi</label>
                 <input type="text" name="title" value="{{ old('title', $classSession->title) }}" required>
             </div>
             <div class="form-group">
                 <label>Waktu Mulai</label>
                 <input type="datetime-local" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($classSession->start_time)->format('Y-m-d\TH:i')) }}" required>
             </div>
             <div class="form-group">
                 <label>Waktu Selesai</label>
                 <input type="datetime-local" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($classSession->end_time)->format('Y-m-d\TH:i')) }}" required>
             </div>
             <input type="hidden" name="subject_name" id="subject_name" value="{{ old('subject_name', $classSession->subject_name) }}">
             <button type="submit">Simpan Perubahan</button>
         </form>
         <a href="{{ route('teacher.lms.index') }}">Kembali</a>
         <script>
             document.getElementById('classroom_id').addEventListener('change', function() {
                 const subjects = @json($subjects);
                 document.getElementById('subject_name').value = subjects[this.value] || '';
             });
             // Set initial subject_name on page load
             document.getElementById('classroom_id').dispatchEvent(new Event('change'));
         </script>
     </body>
     </html>