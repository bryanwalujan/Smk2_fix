<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Dashboard LMS Guru</title>
         <style>
             body { font-family: Arial, sans-serif; margin: 50px; }
             table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
             th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
             th { background-color: #f2f2f2; }
             a { color: blue; margin-right: 10px; }
             .logout-form { display: inline; }
         </style>
     </head>
     <body>
         <h2>Dashboard LMS Guru</h2>
         <div style="margin-bottom: 20px;">
             <a href="{{ route('teacher.dashboard') }}">Kembali ke Dashboard</a>
             <form action="{{ route('logout') }}" method="POST" class="logout-form">
                 @csrf
                 <button type="submit" style="background: none; border: none; color: blue; cursor: pointer; padding: 0;">Logout</button>
             </form>
         </div>
         <h3>Mata Pelajaran Anda</h3>
         <ul>
             @foreach ($subjects as $classroom_id => $subject_name)
                 <li>{{ \App\Models\Classroom::find($classroom_id)->full_name }}: {{ $subject_name }}</li>
             @endforeach
         </ul>
         <h3>Sesi Kelas</h3>
         <a href="{{ route('teacher.lms.create_session') }}">Tambah Sesi Kelas</a>
         @if ($classSessions->isEmpty())
             <p>Tidak ada sesi kelas.</p>
         @else
             <table>
                 <thead>
                     <tr>
                         <th>Judul</th>
                         <th>Kelas</th>
                         <th>Mata Pelajaran</th>
                         <th>Waktu</th>
                         <th>Aksi</th>
                     </tr>
                 </thead>
                 <tbody>
                     @foreach ($classSessions as $session)
                         <tr>
                             <td>{{ $session->title }}</td>
                             <td>{{ $session->classroom->full_name }}</td>
                             <td>{{ $session->subject_name }}</td>
                             <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                             <td>
                                 <a href="{{ route('teacher.lms.show_session', $session) }}">Lihat</a>
                                 <a href="{{ route('teacher.lms.edit_session', $session) }}">Edit</a>
                             </td>
                         </tr>
                     @endforeach
                 </tbody>
             </table>
         @endif
     </body>
     </html>