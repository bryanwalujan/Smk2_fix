<nav class="bg-white shadow-sm">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="{{ route('student.dashboard') }}" class="text-xl font-bold text-black-600">
                LMS Siswa
            </a>
        </div>
        
        <div class="flex items-center space-x-4">
            <a href="{{ route('student.dashboard') }}" 
               class="text-gray-600 hover:text-indigo-600 transition-colors">
                Dashboard
            </a>
            
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="text-gray-600 hover:text-indigo-600 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>