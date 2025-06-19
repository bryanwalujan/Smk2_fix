<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Ganti Password</title>
         <style>
             body { font-family: Arial, sans-serif; margin: 50px; }
             .form-group { margin-bottom: 15px; }
             label { display: block; margin-bottom: 5px; }
             input { padding: 5px; width: 100%; max-width: 300px; }
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
         <h2>Ganti Password</h2>
         @if ($errors->any())
             <div class="error">
                 <ul>
                     @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                     @endforeach
                 </ul>
             </div>
         @endif
         @if (session('success'))
             <div style="color: green; margin-bottom: 15px;">
                 {{ session('success') }}
             </div>
         @endif
         <form method="POST" action="{{ route('teacher.lms.change_password.store') }}">
             @csrf
             <div class="form-group">
                 <label for="current_password">Password Lama</label>
                 <input type="password" name="current_password" id="current_password" required>
             </div>
             <div class="form-group">
                 <label for="new_password">Password Baru</label>
                 <input type="password" name="new_password" id="new_password" required>
             </div>
             <div class="form-group">
                 <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                 <input type="password" name="new_password_confirmation" id="new_password_confirmation" required>
             </div>
             <button type="submit">Ganti Password</button>
         </form>
     </body>
     </html>