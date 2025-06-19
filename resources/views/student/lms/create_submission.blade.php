<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Kumpulkan Tugas</title>
         <style>
             body { font-family: Arial, sans-serif; margin: 50px; }
             .form-group { margin-bottom: 15px; }
             input, textarea { padding: 5px; width: 100%; }
             button { padding: 10px; background-color: #4CAF50; color: white; border: none; }
             .error { color: red; }
         </style>
     </head>
     <body>
         <h2>Kumpulkan Tugas: {{ $assignment->title }}</h2>
         @if ($errors->any())
             <div class="error">
                 <ul>
                     @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                     @endforeach
                 </ul>
             </div>
         @endif
         @if ($existingSubmission)
             <p class="error">Anda sudah mengumpulkan tugas ini.</p>
         @else
             <form method="POST" action="{{ route('student.lms.store_submission', $assignment) }}" enctype="multipart/form-data">
                 @csrf
                 <div class="form-group">
                     <label>File Tugas (PDF, DOC, DOCX, maks 2MB)</label>
                     <input type="file" name="file" accept=".pdf,.doc,.docx">
                 </div>
                 <div class="form-group">
                     <label>Catatan</label>
                     <textarea name="notes" rows="5">{{ old('notes') }}</textarea>
                 </div>
                 <button type="submit">Kumpulkan</button>
             </form>
         @endif
         <a href="{{ route('student.lms.show_session', $assignment->classSession) }}">Kembali</a>
     </body>
     </html>