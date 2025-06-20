<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Tambah Materi</title>
         <style>
             body { font-family: Arial, sans-serif; margin: 50px; }
             .form-group { margin-bottom: 15px; }
             label { display: block; margin-bottom: 5px; }
             input, textarea { padding: 5px; width: 100%; max-width: 500px; }
             button { padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
             .error { color: red; }
             .logout-form { display: inline; }
         </style>
     </head>
     <body>
         <div style="margin-bottom: 20px;">
             <a href="{{ route('teacher.lms.show_session', $classSession) }}">Kembali ke Sesi</a>
             <form action="{{ route('logout') }}" method="POST" class="logout-form">
                 @csrf
                 <button type="submit" style="background: none; border: none; color: blue; cursor: pointer; padding: 0;">Logout</button>
             </form>
         </div>
         <h2>Tambah Materi</h2>
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
                 <label for="title">Judul Materi</label>
                 <input type="text" name="title" id="title" value="{{ old('title') }}" required>
             </div>
             <div class="form-group">
                 <label for="content">Konten</label>
                 <textarea name="content" id="content">{{ old('content') }}</textarea>
             </div>
             <div class="form-group">
                 <label for="file">File (PDF, DOC, PPT, JPG, PNG, GIF, MP4, AVI, MOV, MKV, maks 256 MB)</label>
                 <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.mkv">
             </div>
             <button type="submit">Simpan</button>
         </form>
     </body>
     </html>