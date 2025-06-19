<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Absensi</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @include('layouts.navbar-admin')
    
    <div class="container mx-auto px-4 py-8">
        <!-- Flash Messages Section -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Absensi</h1>
                <p class="text-gray-600">Perbarui data absensi untuk {{ $type == 'student' ? 'siswa' : 'guru' }}</p>
            </div>
            <div>
                <a href="{{ route('attendance.index') }}" 
                   class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar Absensi</span>
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('attendance.update', $attendance->id) }}" class="flex flex-col gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-700">Tipe Pengguna</label>
                    <select name="user_type" id="user_type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onchange="updateUserSelect(this.value)">
                        <option value="student" {{ $type == 'student' ? 'selected' : '' }}>Siswa</option>
                        <option value="teacher" {{ $type == 'teacher' ? 'selected' : '' }}>Guru</option>
                    </select>
                    @error('user_type')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
                    <select name="user_id" id="user_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Pengguna</option>
                        @foreach ($type == 'student' ? $students : $teachers as $id => $name)
                            <option value="{{ $id }}" {{ ($type == 'student' ? $attendance->student_id : $attendance->teacher_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ $attendance->tanggal }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('tanggal')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="waktu_masuk" class="block text-sm font-medium text-gray-700">Waktu Masuk</label>
                    <input type="time" name="waktu_masuk" id="waktu_masuk" 
                           value="{{ $attendance->waktu_masuk ? \Carbon\Carbon::parse($attendance->waktu_masuk)->format('H:i') : '' }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('waktu_masuk')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="waktu_pulang" class="block text-sm font-medium text-gray-700">Waktu Pulang (Opsional)</label>
                    <input type="time" name="waktu_pulang" id="waktu_pulang" 
                           value="{{ $attendance->waktu_pulang ? \Carbon\Carbon::parse($attendance->waktu_pulang)->format('H:i') : '' }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('waktu_pulang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="hadir" {{ $attendance->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ $attendance->status == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ $attendance->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpa" {{ $attendance->status == 'alpa' ? 'selected' : '' }}>Alpa</option>
                    </select>
                    @error('status')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="metode_absen" class="block text-sm font-medium text-gray-700">Metode Absen</label>
                    <select name="metode_absen" id="metode_absen" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="manual" {{ $attendance->metode_absen == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="barcode" {{ $attendance->metode_absen == 'barcode' ? 'selected' : '' }}>Barcode</option>
                    </select>
                    @error('metode_absen')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- JavaScript untuk Mengisi Dropdown Pengguna Dinamis -->
        <script>
            function updateUserSelect(userType) {
                const userSelect = document.getElementById('user_id');
                userSelect.innerHTML = '<option value="">Pilih Pengguna</option>';

                const users = userType === 'student' ? @json($students) : @json($teachers);
                const currentUserId = userType === 'student' ? '{{ $attendance->student_id }}' : '{{ $attendance->teacher_id }}';

                for (const [id, name] of Object.entries(users)) {
                    const option = document.createElement('option');
                    option.value = id;
                    option.text = name;
                    if (id === currentUserId) {
                        option.selected = true;
                    }
                    userSelect.appendChild(option);
                }
            }

            // Panggil fungsi saat halaman dimuat
            document.addEventListener('DOMContentLoaded', () => {
                updateUserSelect(document.getElementById('user_type').value);
            });

            // Validasi waktu pulang
            document.querySelector('form').addEventListener('submit', function(e) {
                const waktuMasuk = document.getElementById('waktu_masuk').value;
                const waktuPulang = document.getElementById('waktu_pulang').value;
                const tanggal = document.getElementById('tanggal').value;

                if (waktuPulang && waktuMasuk) {
                    const masuk = new Date(`${tanggal}T${waktuMasuk}`);
                    const pulang = new Date(`${tanggal}T${waktuPulang}`);
                    if (pulang <= masuk) {
                        e.preventDefault();
                        alert('Waktu pulang harus setelah waktu masuk.');
                    }
                }
            });
        </script>
    </div>
</body>
</html>