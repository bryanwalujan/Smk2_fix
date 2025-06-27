<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    @vite('resources/css/app.css')
</head>
<body class="h-full flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang</h1>
            <h2 class="text-xl font-semibold text-indigo-600">{{ auth()->user()->name }}</h2>
            <p class="text-gray-500">Anda login sebagai siswa</p>
        </div>
        
        <div class="pt-4">
            <a href="{{ route('student.lms.index') }}" 
               class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                Akses LMS
            </a>
        </div>
    </div>
</body>
</html>