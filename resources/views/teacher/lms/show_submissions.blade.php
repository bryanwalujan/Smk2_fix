<!DOCTYPE html>
     <html lang="id">
     <head>
         <title>Detail Pengumpulan Tugas</title>
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
             <a href="{{ route('teacher.lms.show_session', $assignment->classSession) }}">Kembali ke Sesi</a>
             <form action="{{ route('logout') }}" method="POST" class="logout-form">
                 @csrf
                 <button type="submit" style="background: none; border: none; color: blue; cursor: pointer; padding: 0;">Logout</button>
             </form>
         </div>
         <h2>Detail Pengumpulan Tugas: {{ $assignment->title }}</h2>
         <p>Mata Pelajaran: {{ $assignment->classSession->subject_name }}</p>
         <p>Kelas: {{ $assignment->classSession->classroom->full_name }}</p>
         <p>Tenggat Waktu: {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('l H:i') }}</p>
         <h3>Pengumpulan Siswa</h3>
         @if ($assignment->submissions->isEmpty())
             <p>Belum ada siswa yang mengumpulkan tugas.</p>
         @else
             <table>
                 <thead>
                     <tr>
                         <th>Nama Siswa</th>
                         <th>Kelas</th>
                         <th>Jam Pengumpulan</th>
                         <th>File Tugas</th>
                         <th>Catatan</th>
                     </tr>
                 </thead>
                 <tbody>
                     @foreach ($assignment->submissions as $submission)
                         <tr>
                             <td>{{ $submission->student->user->name }}</td>
                             <td>{{ $submission->student->classroom->full_name }}</td>
                             <td>{{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('l H:i') }}</td>
                             <td>
                                 @if ($submission->file_path)
                                     <a href="{{ Storage::url($submission->file_path) }}" target="_blank">Unduh</a>
                                 @else
                                     Tidak ada file
                                 @endif
                             </td>
                             <td>{{ $submission->notes ?: '-' }}</td>
                         </tr>
                     @endforeach
                 </tbody>
             </table>
         @endif
     </body>
     </html>