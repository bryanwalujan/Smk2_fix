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
       </style>
   </head>
   <body>
       <h2>Detail Sesi: {{ $classSession->title }}</h2>
       <p>Kelas: {{ $classSession->classroom->full_name }}</p>
       <p>Mata Pelajaran: {{ $classSession->subject_name }}</p>
       <p>Waktu: {{ $classSession->start_time }} - {{ $classSession->end_time }}</p>
       <h3>Materi</h3>
       <a href="{{ route('teacher.lms.create_material', $classSession) }}">Tambah Materi</a>
       @if ($classSession->materials->isEmpty())
           <p>Tidak ada materi.</p>
       @else
           <table>
               <thead>
                   <tr>
                       <th>Judul</th>
                       <th>Konten</th>
                       <th>File</th>
                   </tr>
               </thead>
               <tbody>
                   @foreach ($classSession->materials as $material)
                       <tr>
                           <td>{{ $material->title }}</td>
                           <td>{{ $material->content ?? '-' }}</td>
                           <td>
                               @if ($material->file_path)
                                   <a href="{{ Storage::url($material->file_path) }}" target="_blank">Download</a>
                               @else
                                   -
                               @endif
                           </td>
                       </tr>
                   @endforeach
               </tbody>
           </table>
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
                       <th>Deadline</th>
                       <th>Pengumpulan</th>
                   </tr>
               </thead>
               <tbody>
                   @foreach ($classSession->assignments as $assignment)
                       <tr>
                           <td>{{ $assignment->title }}</td>
                           <td>{{ $assignment->description ?? '-' }}</td>
                           <td>{{ $assignment->deadline }}</td>
                           <td>{{ $assignment->submissions->count() }} siswa</td>
                       </tr>
                   @endforeach
               </tbody>
           </table>
       @endif
       <a href="{{ route('teacher.lms.index') }}">Kembali</a>
   </body>
   </html>