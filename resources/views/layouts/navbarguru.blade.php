<!-- resources/views/layouts/navbarguru.blade.php -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Mobile menu button (hidden on desktop) -->
            <div class="flex md:hidden">
                <button type="button" id="mobile-menu-button" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                        aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <!-- Hamburger icon -->
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <!-- Close icon -->
                    <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Logo/Brand (centered on mobile) -->
            <div class="flex-1 flex items-center justify-center md:justify-start">
                <a href="{{ route('teacher.lms.index') }}" class="text-xl font-bold text-gray-800 whitespace-nowrap">
                    LMS Guru
                </a>
            </div>

            <!-- Desktop Navigation (hidden on mobile) -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('teacher.lms.change_password') }}" 
                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-50">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Ganti Password
                    </span>
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-50">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                            </svg>
                            Logout
                        </span>
                    </button>
                </form>

                <!-- User profile dropdown -->
                <div class="ml-3 relative">
                    <div class="flex items-center space-x-2 cursor-pointer group">
                        <span class="text-gray-600 text-sm hidden lg:inline">{{ auth()->user()->name }}</span>
                        <img class="h-8 w-8 rounded-full border-2 border-transparent group-hover:border-blue-500 transition-colors" 
                             src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff&bold=true" 
                             alt="User profile">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu (hidden by default) -->
    <div class="hidden md:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('teacher.lms.change_password') }}" 
               class="block px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    Ganti Password
                </span>
            </a>
            
            <form action="{{ route('logout') }}" method="POST" class="block">
                @csrf
                <button type="submit" 
                        class="w-full text-left px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                        </svg>
                        Logout
                    </span>
                </button>
            </form>
            
            <div class="px-4 py-2 flex items-center">
                <img class="h-8 w-8 rounded-full mr-3" 
                     src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff&bold=true" 
                     alt="User profile">
                <span class="text-gray-600 text-base">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle functionality
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const isHidden = menu.classList.contains('hidden');
        
        // Toggle menu visibility
        menu.classList.toggle('hidden');
        
        // Toggle hamburger/close icon
        const svgs = this.querySelectorAll('svg');
        svgs.forEach(svg => svg.classList.toggle('hidden'));
    });
</script>