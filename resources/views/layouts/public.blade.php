<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SMK Negeri 2 Tondano</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'school-red': '#ff020a',
                        'school-red-dark': '#d60000',
                    },
                },
            },
        }
    </script>
    <!-- Font Poppins dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }
        .header-gradient {
            background: linear-gradient(135deg, #ff020a 0%, #d60000 100%);
        }
        .footer-gradient {
            background: linear-gradient(135deg, #d60000 0%, #ff020a 100%);
        }
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-scale:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="header-gradient text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-3">
                <!-- Logo -->
                <div class="bg-white p-1 rounded-full shadow">
                 <img src="{{ asset('images/logo-smkn2.png') }}" alt="Logo SMK Negeri 2 Tondano" class="h-12">
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">SMK Negeri 2 Tondano</h1>
                    <p class="text-xs text-red-200 hidden md:block">Berprestasi, Berkarakter, Berwawasan Global</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="mt-2 md:mt-0 bg-white text-school-red px-4 py-2 rounded-full font-semibold hover:bg-gray-100 transition flex items-center space-x-2 shadow-md hover:shadow-lg">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        </div>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer class="footer-gradient text-white py-8">
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
                        <li><a href="#" class="text-red-100 hover:text-white transition flex items-center">
                            <i class="fas fa-arrow-right mr-2 text-xs"></i>PPDB Online
                        </a></li>
                        <li><a href="#" class="text-red-100 hover:text-white transition flex items-center">
                            <i class="fas fa-arrow-right mr-2 text-xs"></i>E-Learning
                        </a></li>
                        <li><a href="#" class="text-red-100 hover:text-white transition flex items-center">
                            <i class="fas fa-arrow-right mr-2 text-xs"></i>Perpustakaan Digital
                        </a></li>
                        <li><a href="#" class="text-red-100 hover:text-white transition flex items-center">
                            <i class="fas fa-arrow-right mr-2 text-xs"></i>Alumni
                        </a></li>
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
                        <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition hover-scale">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition hover-scale">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition hover-scale">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="bg-white text-school-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition hover-scale">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-4">Newsletter</h4>
                        <div class="flex">
                            <input type="email" placeholder="Email Anda" class="px-4 py-2 rounded-l-lg w-full text-gray-700 focus:outline-none">
                            <button class="bg-white text-school-red px-4 py-2 rounded-r-lg font-semibold hover:bg-gray-100 transition">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-red-400 mt-8 pt-6 text-center text-red-200">
                <p>&copy; {{ date('Y') }} SMK Negeri 2 Tondano. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
</body>
</html>