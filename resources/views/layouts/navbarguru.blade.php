<!-- resources/views/layouts/navbarguru.blade.php -->
<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Mobile menu button -->
            <div class="flex md:hidden">
                <button type="button" id="mobile-menu-button" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
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

            <!-- Logo/Brand -->
            <div class="flex-1 flex items-center md:justify-start">
                <a href="{{ route('teacher.lms.index') }}" class="flex items-center">
                    <div class="bg-blue-600 p-2 rounded-lg mr-3">
                        <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-800 whitespace-nowrap hidden sm:block">
                        LMS Guru
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-2">
                <!-- Change Password -->
                <a href="{{ route('teacher.lms.change_password') }}" 
                   class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-blue-50 flex items-center">
                    <i class="fas fa-key mr-2 text-blue-500"></i>
                    <span>Ganti Password</span>
                </a>
                
                <!-- User Profile Dropdown -->
                <div class="ml-2 relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none group">
                        <span class="text-gray-700 text-sm font-medium hidden lg:inline">{{ auth()->user()->name }}</span>
                        <div class="relative">
                            <img class="h-8 w-8 rounded-full border-2 border-transparent group-hover:border-blue-500 transition-all" 
                                 src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff&bold=true" 
                                 alt="User profile">
                            <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full bg-green-500 ring-2 ring-white"></span>
                        </div>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" 
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95">
                        <div class="py-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2 text-gray-500"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="hidden md:hidden bg-white border-t border-gray-200" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Change Password -->
            <a href="{{ route('teacher.lms.change_password') }}" 
               class="block px-4 py-3 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 border-b border-gray-100 flex items-center">
                <i class="fas fa-key mr-3 text-blue-500"></i>
                Ganti Password
            </a>
            
            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" class="block border-b border-gray-100">
                @csrf
                <button type="submit" 
                        class="w-full text-left px-4 py-3 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 flex items-center">
                    <i class="fas fa-sign-out-alt mr-3 text-gray-500"></i>
                    Logout
                </button>
            </form>
            
            <!-- User Profile -->
            <div class="px-4 py-3 flex items-center border-t border-gray-100">
                <img class="h-10 w-10 rounded-full mr-3" 
                     src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff&bold=true" 
                     alt="User profile">
                <div>
                    <p class="text-gray-800 font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">Guru</p>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Include AlpineJS for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    // Mobile menu toggle functionality
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const isHidden = menu.classList.contains('hidden');
        
        // Toggle menu visibility with animation
        if (isHidden) {
            menu.classList.remove('hidden');
            menu.style.maxHeight = '0';
            setTimeout(() => {
                menu.style.maxHeight = menu.scrollHeight + 'px';
            }, 10);
        } else {
            menu.style.maxHeight = '0';
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 300);
        }
        
        // Toggle hamburger/close icon
        const svgs = this.querySelectorAll('svg');
        svgs.forEach(svg => svg.classList.toggle('hidden'));
    });
</script>

<style>
    #mobile-menu {
        transition: max-height 0.3s ease-out;
        overflow: hidden;
        max-height: 0;
    }
    
    .nav-item {
        position: relative;
    }
    
    .nav-item:hover::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #3b82f6;
        animation: underline 0.3s ease-out;
    }
    
    @keyframes underline {
        from { width: 0; }
        to { width: 100%; }
    }
</style>