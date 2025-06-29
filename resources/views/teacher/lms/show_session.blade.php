
@extends('layouts.appguru')

@section('title', 'Detail Sesi Kelas')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Sesi Kelas</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Kelola materi, tugas, dan absensi untuk sesi ini</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="{{ route('teacher.lms.create_material', $classSession) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-book mr-2"></i> Tambah Materi
                </a>
                <a href="{{ route('teacher.lms.create_assignment', $classSession) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-tasks mr-2"></i> Tambah Tugas
                </a>
                <a href="{{ route('teacher.lms.show_attendance', $classSession) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-clipboard-list mr-2"></i> Lihat Absensi Siswa
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8 rounded-lg flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Session Details Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8 border border-gray-200">
            <div class="p-5 md:p-6">
                <div class="flex flex-col md:flex-row justify-between gap-4">
                    <div class="flex-1">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-indigo-500"></i>
                            <span>Informasi Sesi</span>
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Class Info -->
                            <div class="flex items-start gap-3">
                                <div class="bg-indigo-100 p-2 rounded-lg">
                                    <i class="fas fa-chalkboard-teacher text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Kelas</p>
                                    <p class="font-medium text-gray-800">{{ $classSession->classroom->full_name }}</p>
                                </div>
                            </div>
                            
                            <!-- Subject Info -->
                            <div class="flex items-start gap-3">
                                <div class="bg-indigo-100 p-2 rounded-lg">
                                    <i class="fas fa-book text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Mata Pelajaran</p>
                                    <p class="font-medium text-gray-800">{{ $classSession->subject ? $classSession->subject->name : 'Tidak Ada' }}</p>
                                </div>
                            </div>
                            
                            <!-- Day Info -->
                            <div class="flex items-start gap-3">
                                <div class="bg-indigo-100 p-2 rounded-lg">
                                    <i class="fas fa-calendar-day text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Hari</p>
                                    <p class="font-medium text-gray-800">{{ $classSession->day }}</p>
                                </div>
                            </div>
                            
                            <!-- Time Info -->
                            <div class="flex items-start gap-3">
                                <div class="bg-indigo-100 p-2 rounded-lg">
                                    <i class="fas fa-clock text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Waktu</p>
                                    <p class="font-medium text-gray-800">{{ $classSession->start_time->format('H:i') }} - {{ $classSession->end_time->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materials Section -->
        <section class="mb-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-book-open text-indigo-500"></i>
                    <span>Materi Pembelajaran</span>
                </h2>
                <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: {{ $classSession->materials->count() }} Materi
                </div>
            </div>

            @if($classSession->materials->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-gray-200">
                    <div class="bg-indigo-50 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-book-open text-indigo-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Materi</h3>
                    <p class="text-gray-500 mb-4 max-w-md mx-auto">Tambahkan materi pertama untuk membantu siswa memahami pelajaran dengan lebih baik</p>
                    <a href="{{ route('teacher.lms.create_material', $classSession) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Tambah Materi
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($classSession->materials as $material)
                        <div class="bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all overflow-hidden">
                            <div class="p-5">
                                <div class="flex justify-between items-start gap-3 mb-4">
                                    <h3 class="font-medium text-gray-800 line-clamp-2">{{ $material->title }}</h3>
                                    <div class="relative">
                                        <button class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition-colors material-menu-btn">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="absolute right-0 mt-1 w-40 bg-white rounded-md shadow-lg z-10 hidden border border-gray-200 material-menu">
                                            <a href="{{ route('teacher.lms.show_material', [$classSession, $material]) }}"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                <i class="fas fa-eye text-blue-500"></i> Lihat
                                            </a>
                                            <a href="{{ route('teacher.lms.edit_material', [$classSession, $material]) }}"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                <i class="fas fa-edit text-yellow-500"></i> Edit
                                            </a>
                                            <form action="{{ route('teacher.lms.destroy_material', [$classSession, $material]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="return confirm('Yakin ingin menghapus materi ini?')">
                                                    <i class="fas fa-trash text-red-500"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-500 mb-4 gap-2">
                                    <i class="far fa-clock text-gray-400"></i>
                                    <span>Dibuat: {{ $material->created_at->format('d M Y') }}</span>
                                </div>
                                
                                @if ($material->file_path)
                                    <div class="flex items-center bg-blue-50 rounded-lg p-3 mb-4 gap-3">
                                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                                        <div class="truncate flex-1">
                                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium truncate block">
                                                {{ basename($material->file_path) }}
                                            </a>
                                            <p class="text-xs text-gray-500 mt-1">Klik untuk mengunduh</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                    <a href="{{ route('teacher.lms.show_material', [$classSession, $material]) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center gap-1">
                                        <i class="fas fa-external-link-alt text-xs"></i> Buka Materi
                                    </a>
                                    <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-600">
                                        Materi
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Assignments Section -->
        <section class="mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-tasks text-green-500"></i>
                    <span>Tugas Kelas</span>
                </h2>
                <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Total: {{ $classSession->assignments->count() }} Tugas
                </div>
            </div>

            @if($classSession->assignments->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-gray-200">
                    <div class="bg-green-50 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-tasks text-green-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Tugas</h3>
                    <p class="text-gray-500 mb-4 max-w-md mx-auto">Buat tugas untuk mengevaluasi pemahaman siswa terhadap materi</p>
                    <a href="{{ route('teacher.lms.create_assignment', $classSession) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Tambah Tugas
                    </a>
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Tugas</th>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenggat</th>
                                    <th scope="col" class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($classSession->assignments as $assignment)
                                    @php
                                        $now = now();
                                        $deadline = Carbon\Carbon::parse($assignment->deadline);
                                        $status = $deadline->isPast() ? 'Lewat Tenggat' : 'Aktif';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <!-- Assignment Title -->
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-green-100 p-2 rounded-lg">
                                                    <i class="fas fa-tasks text-green-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $assignment->title }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">Dibuat: {{ $assignment->created_at->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Status -->
                                        <td class="px-5 py-4">
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        
                                        <!-- Deadline -->
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $deadline->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $deadline->format('H:i') }}</div>
                                        </td>
                                        
                                        <!-- Actions -->
                                        <td class="px-5 py-4 text-right">
                                            <div class="flex justify-end items-center gap-1">
                                                <a href="{{ route('teacher.lms.show_submissions', $assignment) }}"
                                                   class="p-2 text-green-600 hover:text-green-800 rounded-full hover:bg-green-50 transition-colors"
                                                   data-tooltip="Pengumpulan">
                                                    <i class="fas fa-inbox text-sm"></i>
                                                </a>
                                                <a href="{{ route('teacher.lms.show_assignment', [$classSession, $assignment]) }}"
                                                   class="p-2 text-blue-600 hover:text-blue-800 rounded-full hover:bg-blue-50 transition-colors"
                                                   data-tooltip="Detail">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </a>
                                                <a href="{{ route('teacher.lms.edit_assignment', [$classSession, $assignment]) }}"
                                                   class="p-2 text-yellow-600 hover:text-yellow-800 rounded-full hover:bg-yellow-50 transition-colors"
                                                   data-tooltip="Edit">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </a>
                                                <form action="{{ route('teacher.lms.destroy_assignment', [$classSession, $assignment]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-50 transition-colors"
                                                            data-tooltip="Hapus"
                                                            onclick="return confirm('Yakin ingin menghapus tugas ini?')">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <!-- Tooltip Element -->
    <div id="tooltip" class="absolute z-50 invisible inline-block px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded-md shadow-sm opacity-0 transition-opacity"></div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
        // Material dropdown menus
        document.querySelectorAll('.material-menu-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const menu = this.nextElementSibling;
                document.querySelectorAll('.material-menu').forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });
                menu.classList.toggle('hidden');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.material-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        });

        // Tooltip functionality
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                const tooltip = document.getElementById('tooltip');
                tooltip.textContent = this.getAttribute('data-tooltip');
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = `${rect.left + rect.width/2 - tooltip.offsetWidth/2}px`;
                tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5}px`;
                
                tooltip.classList.remove('invisible', 'opacity-0');
                tooltip.classList.add('visible', 'opacity-100');
            });
            
            el.addEventListener('mouseleave', function() {
                const tooltip = document.getElementById('tooltip');
                tooltip.classList.add('invisible', 'opacity-0');
                tooltip.classList.remove('visible', 'opacity-100');
            });
        });
    </script>
@endsection
