<!DOCTYPE html>
   <html lang="id">
   <head>
       <title>Tambah Materi</title>
       <style>
           body { font-family: Arial, sans-serif; margin: 50px; }
           .form-group { margin-bottom: 15px; }
           input, textarea { padding: 5px; width: 100%; }
           button { padding: 10px; background-color: #4CAF50; color: white; border: none; }
           .error { color: red; }
       </style>
   </head>
   <body>
       <h2>Tambah Materi untuk {{ $classSession->title }}</h2>
       @if ($errors->any())
           <div class="error">
               <ul>
                   @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                   @endforeach
               </ul>
           </div>
       @endif
       <form method="POST" action="{{ route('teacher.lms.store_material', $classSession) }}" enctype="multipart/form-data">
           @csrf
           <div class="form-group">
               <label>Judul Materi</label>
               <input type="text" name="title" required>
           </div>
           <div class="form-group">
               <label>Konten (Teks)</label>
               <textarea name="content" rows="5"></textarea>
           </div>
           <div class="form-group">
               <label>File (PDF/Doc)</label>
               <input type="file" name="file" accept=".pdf,.doc,.docx">
           </div>
           <button type="submit">Simpan</button>
       </form>
       <a href="{{ route('teacher.lms.show_session', $classSession) }}">Kembali</a>
   </body>
   </html>