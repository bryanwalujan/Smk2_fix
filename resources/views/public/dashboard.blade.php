@extends('layouts.public')

@section('title', 'Dashboard')

@section('content')
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">



<div class="flex flex-col md:flex-row min-h-screen">
    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-white shadow-lg md:fixed h-auto md:h-full">
        <nav class="mt-6">
            <ul class="space-y-1">
                <li>
                    <a href="#posts" class="flex items-center px-4 py-3 text-gray-700 hover:bg-school-red hover:text-white transition group">
                        <i class="fas fa-newspaper w-6 text-center group-hover:text-white text-school-red mr-3"></i>
                        <span>Berita</span>
                    </a>
                </li>
                <li>
                    <a href="#events" class="flex items-center px-4 py-3 text-gray-700 hover:bg-school-red hover:text-white transition group">
                        <i class="fas fa-calendar-alt w-6 text-center group-hover:text-white text-school-red mr-3"></i>
                        <span>Acara</span>
                    </a>
                </li>
                <li>
                    <a href="#gallery" class="flex items-center px-4 py-3 text-gray-700 hover:bg-school-red hover:text-white transition group">
                        <i class="fas fa-images w-6 text-center group-hover:text-white text-school-red mr-3"></i>
                        <span>Galeri</span>
                    </a>
                </li>
                <li>
                    <a href="#vision-mission" class="flex items-center px-4 py-3 text-gray-700 hover:bg-school-red hover:text-white transition group">
                        <i class="fas fa-bullseye w-6 text-center group-hover:text-white text-school-red mr-3"></i>
                        <span>Visi Misi</span>
                    </a>
                </li>
                <li>
                    <a href="#about-us" class="flex items-center px-4 py-3 text-gray-700 hover:bg-school-red hover:text-white transition group">
                        <i class="fas fa-info-circle w-6 text-center group-hover:text-white text-school-red mr-3"></i>
                        <span>Tentang Kami</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Quick Contact -->
        <div class="mt-8 mx-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="font-semibold text-school-red mb-2 flex items-center">
                <i class="fas fa-phone-alt mr-2"></i>
                Kontak
            </h3>
            <p class="text-sm text-gray-600">
                <i class="fas fa-map-marker-alt mr-2 text-school-red"></i> 
                Jl. Raya Tondano, Minahasa
            </p>
            <p class="text-sm text-gray-600 mt-1">
                <i class="fas fa-envelope mr-2 text-school-red"></i> 
                info@smkn2tondano.sch.id
            </p>
            <div class="flex space-x-3 mt-3">
                <a href="#" class="text-school-red hover:text-red-800">
                    <i class="fab fa-facebook-square text-xl"></i>
                </a>
                <a href="#" class="text-school-red hover:text-red-800">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <a href="#" class="text-school-red hover:text-red-800">
                    <i class="fab fa-youtube text-xl"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="md:ml-64 flex-1 container mx-auto px-4 py-8">
        <!-- Hero Banner -->
        <div class="mb-10 rounded-xl overflow-hidden shadow-lg relative h-64">
            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-full flex items-center justify-center">
                <span class="text-gray-500">Banner Utama</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6">
                <h2 class="text-2xl font-bold text-white">Selamat Datang di SMK Negeri 2 Tondano</h2>
                <p class="text-white opacity-90">Sekolah Berbasis Teknologi dan Industri</p>
            </div>
        </div>

        <!-- Posts Section -->
        <section id="posts" class="mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-school-red flex items-center">
                    <i class="fas fa-newspaper mr-3"></i>
                    Berita Terbaru
                </h2>
                <a href="#" class="text-school-red hover:text-red-800 font-medium flex items-center">
                    Lihat Semua
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Post 1 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition transform hover:scale-[1.02] hover:shadow-lg">
                    <div class="bg-gray-200 border-2 border-dashed w-full h-48"></div>
                    <div class="p-5">
                        <div class="flex items-center text-gray-500 text-sm mb-2">
                            <i class="far fa-calendar mr-2"></i>
                            15 Juni 2025
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Pelatihan Industri bagi Guru SMK</h3>
                        <p class="text-gray-600 mb-4">Program peningkatan kompetensi guru dalam menghadapi revolusi industri 4.0...</p>
                        <a href="#" class="text-school-red font-semibold hover:text-red-800 transition flex items-center">
                            Baca Selengkapnya
                            <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Post 2 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition transform hover:scale-[1.02] hover:shadow-lg">
                    <div class="bg-gray-200 border-2 border-dashed w-full h-48"></div>
                    <div class="p-5">
                        <div class="flex items-center text-gray-500 text-sm mb-2">
                            <i class="far fa-calendar mr-2"></i>
                            10 Juni 2025
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Siswa SMK Juara Lomba Robotik Nasional</h3>
                        <p class="text-gray-600 mb-4">Tim robotik SMK Negeri 2 Tondano berhasil meraih juara 1 dalam kompetisi...</p>
                        <a href="#" class="text-school-red font-semibold hover:text-red-800 transition flex items-center">
                            Baca Selengkapnya
                            <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Post 3 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition transform hover:scale-[1.02] hover:shadow-lg">
                    <div class="bg-gray-200 border-2 border-dashed w-full h-48"></div>
                    <div class="p-5">
                        <div class="flex items-center text-gray-500 text-sm mb-2">
                            <i class="far fa-calendar mr-2"></i>
                            5 Juni 2025
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Penerimaan Siswa Baru TA 2025/2026</h3>
                        <p class="text-gray-600 mb-4">Pendaftaran siswa baru tahun ajaran 2025/2026 telah dibuka. Segera daftarkan...</p>
                        <a href="#" class="text-school-red font-semibold hover:text-red-800 transition flex items-center">
                            Baca Selengkapnya
                            <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Section -->
        <section id="events" class="mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-school-red flex items-center">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Acara Mendatang
                </h2>
                <a href="#" class="text-school-red hover:text-red-800 font-medium flex items-center">
                    Kalender Akademik
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Event 1 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition transform hover:scale-[1.01]">
                    <div class="p-5 border-l-4 border-school-red">
                        <div class="flex items-start">
                            <div class="bg-school-red text-white rounded-lg py-3 px-4 text-center mr-4">
                                <div class="text-2xl font-bold">25</div>
                                <div class="text-sm">JUN</div>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-1">Pameran Karya Siswa</h3>
                                <div class="flex items-center text-gray-600 mb-2">
                                    <i class="far fa-clock mr-2"></i>
                                    08:00 - 15:00 WITA
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Aula SMK N 2 Tondano
                                </div>
                                <p class="mt-3 text-gray-700">Pameran hasil karya siswa dari berbagai jurusan selama satu semester terakhir...</p>
                                <a href="#" class="mt-3 inline-block text-school-red font-semibold hover:text-red-800 transition flex items-center">
                                    Detail Acara
                                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Event 2 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden transition transform hover:scale-[1.01]">
                    <div class="p-5 border-l-4 border-school-red">
                        <div class="flex items-start">
                            <div class="bg-school-red text-white rounded-lg py-3 px-4 text-center mr-4">
                                <div class="text-2xl font-bold">30</div>
                                <div class="text-sm">JUN</div>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-1">Seminar Kewirausahaan</h3>
                                <div class="flex items-center text-gray-600 mb-2">
                                    <i class="far fa-clock mr-2"></i>
                                    09:00 - 12:00 WITA
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Ruang Multimedia
                                </div>
                                <p class="mt-3 text-gray-700">Seminar dengan tema "Memulai Bisnis di Era Digital" menghadirkan praktisi industri...</p>
                                <a href="#" class="mt-3 inline-block text-school-red font-semibold hover:text-red-800 transition flex items-center">
                                    Detail Acara
                                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section id="gallery" class="mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-school-red flex items-center">
                    <i class="fas fa-images mr-3"></i>
                    Galeri Sekolah
                </h2>
                <a href="#" class="text-school-red hover:text-red-800 font-medium flex items-center">
                    Lihat Album
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="relative group rounded-xl overflow-hidden shadow-md h-48">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-full"></div>
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="text-white font-medium">Kegiatan Belajar</span>
                    </div>
                </div>
                <div class="relative group rounded-xl overflow-hidden shadow-md h-48">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-full"></div>
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="text-white font-medium">Praktikum</span>
                    </div>
                </div>
                <div class="relative group rounded-xl overflow-hidden shadow-md h-48">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-full"></div>
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="text-white font-medium">Ekstrakurikuler</span>
                    </div>
                </div>
                <div class="relative group rounded-xl overflow-hidden shadow-md h-48">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-full"></div>
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="text-white font-medium">Fasilitas</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section id="vision-mission" class="mb-16">
            <h2 class="text-3xl font-bold text-school-red mb-6 flex items-center">
                <i class="fas fa-bullseye mr-3"></i>
                Visi dan Misi
            </h2>
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-1/2 bg-school-red text-white p-8">
                        <div class="flex items-center mb-4">
                            <div class="bg-white text-school-red rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                <i class="fas fa-eye text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Visi</h3>
                        </div>
                        <p class="text-lg leading-relaxed">Menjadi sekolah kejuruan unggulan yang menghasilkan lulusan kompeten, berkarakter, dan berdaya saing global.</p>
                    </div>
                    <div class="md:w-1/2 p-8">
                        <div class="flex items-center mb-4">
                            <div class="bg-school-red text-white rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                <i class="fas fa-list-ol text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800">Misi</h3>
                        </div>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-school-red mt-1 mr-3"></i>
                                <span>Menyelenggarakan pendidikan berbasis kompetensi dan teknologi terkini</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-school-red mt-1 mr-3"></i>
                                <span>Meningkatkan kualitas tenaga pendidik dan kependidikan secara berkelanjutan</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-school-red mt-1 mr-3"></i>
                                <span>Membangun kemitraan strategis dengan dunia industri dan usaha</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-school-red mt-1 mr-3"></i>
                                <span>Mengembangkan lingkungan belajar yang kondusif dan berwawasan lingkungan</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="about-us" class="mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-school-red flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    Tentang Kami
                </h2>
                <a href="#" class="text-school-red hover:text-red-800 font-medium flex items-center">
                    Profil Lengkap
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-1/3 p-6 bg-gradient-to-b from-school-red to-red-800">
                        <div class="text-center text-white">
                            <div class="bg-white text-school-red rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-school text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">SMK Negeri 2 Tondano</h3>
                            <p class="opacity-90">"Berprestasi, Berkarakter, Berwawasan Global"</p>
                        </div>
                    </div>
                    <div class="md:w-2/3 p-8">
                        <p class="text-gray-700 mb-4 leading-relaxed">
                            SMK Negeri 2 Tondano adalah sekolah kejuruan negeri yang berdiri sejak tahun 1980. 
                            Kami berkomitmen untuk mencetak generasi muda yang siap kerja dan berwirausaha melalui 
                            pendidikan berbasis kompetensi dan teknologi terkini.
                        </p>
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="flex items-center">
                                <div class="bg-school-red text-white rounded-lg p-3 mr-4">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-school-red">1200+</div>
                                    <div class="text-gray-600">Siswa Aktif</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-school-red text-white rounded-lg p-3 mr-4">
                                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-school-red">85+</div>
                                    <div class="text-gray-600">Tenaga Pendidik</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-school-red text-white rounded-lg p-3 mr-4">
                                    <i class="fas fa-building text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-school-red">7</div>
                                    <div class="text-gray-600">Program Keahlian</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-school-red text-white rounded-lg p-3 mr-4">
                                    <i class="fas fa-handshake text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-school-red">50+</div>
                                    <div class="text-gray-600">Industri Mitra</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Footer -->
<footer class="bg-gradient-to-r from-school-red to-red-800 text-white py-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-school mr-2"></i>
                    SMKN 2 Tondano
                </h3>
                <p class="text-red-100">Sekolah kejuruan berbasis teknologi dan industri di Sulawesi Utara</p>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">Tautan Cepat</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-red-100 hover:text-white transition">PPDB Online</a></li>
                    <li><a href="#" class="text-red-100 hover:text-white transition">E-Learning</a></li>
                    <li><a href="#" class="text-red-100 hover:text-white transition">Perpustakaan Digital</a></li>
                    <li><a href="#" class="text-red-100 hover:text-white transition">Alumni</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                <ul class="space-y-2 text-red-100">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                        Jl. Raya Tondano, Minahasa, Sulawesi Utara
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone-alt mt-1 mr-3"></i>
                        (0431) 123456
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-3"></i>
                        info@smkn2tondano.sch.id
                    </li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">Media Sosial</h4>
                <div class="flex space-x-4">
                    <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="border-t border-red-400 mt-8 pt-6 text-center text-red-200">
            <p>&copy; 2025 SMK Negeri 2 Tondano. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</footer>
@endsection