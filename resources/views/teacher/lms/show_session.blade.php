<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Detail Sesi Kelas</title>
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
         <div style="margin-bottom: 20px;">
             <a href="{{ route('teacher.lms.index') }}">Kembali ke LMS</a>
             <form action="{{ route('logout') }}" method="POST" class="logout-form">
                 @csrf
                 <button type="submit" style="background: none; border: none; color: blue; cursor: pointer; padding: 0;">Logout</button>
             </form>
         </div>
         <h2>Sesi: {{ $classSession->title }}</h2>
         <p>Kelas: {{ $classSession->classroom->full_name }}</p>
         <p>Mata Pelajaran: {{ $classSession->subject_name }}</p>
         <p>Waktu: {{ \Carbon\Carbon::parse($classSession->start_time)->translatedFormat('l H:i') }}-{{ \Carbon\Carbon::parse($classSession->end_time)->format('H:i') }}</p>
         <h3>Materi</h3>
         <a href="{{ route('teacher.lms.create_material', $classSession) }}">Tambah Materi</a>
         @if ($classSession->materials->isEmpty())
             <p>Tidak ada materi.</p>
         @else
             <ul>
                 @foreach ($classSession->materials as $material)
                     <li>
                         {{ $material->title }}
                         @if ($material->file_path)
                             <a href="{{ Storage::url($material->file_path) }}" target="_blank">Unduh</a>
                         @endif
                         @if ($material->content)
                             <p>{{ $material->content }}</p>
                         @endif
                     </li>
                 @endforeach
             </ul>
         @endif
         <h3>Tugas</h3>
         <a href="{{ route('teacher.lms.create_assignment', $classSession) }}">Tambah Tugas</a>
         @if ($classSession->assignments->isEmpty())
             <p>Tidak ada tugas.</p>
         @else
             <table>
                 <thead>
                     <tr>
                         <th>Judul</th>
                         <th>Deskripsi</th>
                         <th>Tenggat Waktu</th>
                         <th>Pengumpulan</th>
                     </tr>
                 </thead>
                 <tbody>
                     @foreach ($classSession->assignments as $assignment)
                         <tr>
                             <td>{{ $assignment->title }}</td>
                             <td>{{ $assignment->description }}</td>
                             <td>{{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('l H:i') }}</td>
                             <td>
                                 <a href="{{ route('teacher.lms.show_submissions', $assignment) }}">
                                     {{ $assignment->submissions->count() }} siswa
                                 </a>
                             </td>
                         </tr>
                     @endforeach
                 </tbody>
             </table>
         @endif
     </body>
     </html>