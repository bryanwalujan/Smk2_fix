<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard LMS Siswa')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navbarstudent')
        
        <main class="flex-grow container mx-auto px-4 py-6">
            @yield('content')
        </main>
        
        <footer class="bg-white border-t py-4 mt-8">
            <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} LMS Sekolah. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>