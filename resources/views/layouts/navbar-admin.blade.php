
@php
    $currentRoute = Route::currentRouteName();
@endphp

<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <div class="bg-indigo-600 p-2 rounded-lg mr-2">
                        <i class="fas fa-school text-white text-xl"></i>
                    </div>
                    <span class="ml-2 text-xl font-bold text-gray-800 hidden sm:inline">SMKN 3 TONDANO</span>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-2">
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ $currentRoute == 'admin.dashboard' ? ' text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 nav-link">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('teachers.index') }}"
                        class="{{ $currentRoute == 'teachers.index' ? ' text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 nav-link">
                        <i class="fas fa-chalkboard-teacher mr-2"></i> Guru
                    </a>
                    <a href="{{ route('students.index') }}"
                        class="{{ $currentRoute == 'students.index' ? ' text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 nav-link">
                        <i class="fas fa-users mr-2"></i> Siswa
                    </a>
                    <a href="{{ route('classrooms.index') }}"
                        class="{{ $currentRoute == 'classrooms.index' ? ' text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 nav-link">
                        <i class="fas fa-door-open mr-2"></i> Kelas
                    </a>
                    <a href="{{ route('subjects.index') }}"
                        class="{{ $currentRoute == 'subjects.index' ? ' text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 nav-link">
                        <i class="fas fa-book mr-2"></i> Mata Pelajaran
                    </a>
                    <a href="{{ route('admin.schedules.index') }}"
                        class="{{ $currentRoute == 'admin.schedules.index' ? ' text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 nav-link">
                        <i class="fas fa-calendar mr-2"></i> Jadwal
                    </a>
                    <a href="{{ route('attendance.index') }}"
                        class="{{ $currentRoute == 'attendance.index' ? ' text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 nav-link">
                        <i class="fas fa-clipboard-check mr-2"></i> Presensi
                    </a>
                </div>
            </div>
            
            <!-- Profile dropdown -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center" x-data="{ open: false }">
                <div class="ml-3 relative">
                    <button @click="open = !open" type="button"
                        class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
                        id="user-menu" aria-expanded="false" aria-haspopup="true">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-8 w-8 rounded-full border-2 border-transparent hover:border-indigo-300"
                            src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}"
                            alt="">
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div x-show="open" @click.away="open = false" 
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95">
                        <div class="py-1">
                            
                            <a href="{{ route('admin.permissions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-key mr-2 text-gray-500"></i> Manajemen Izin
                            </a>
                            <a href="{{ route('logout') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2 text-gray-500"></i> Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <div class="ml-3">
                    <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-indigo-500 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition duration-200"
                    aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars" id="menu-icon"></i>
                    <i class="fas fa-times hidden" id="close-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                class="{{ $currentRoute == 'admin.dashboard' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="{{ route('teachers.index') }}"
                class="{{ $currentRoute == 'teachers.index' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Guru
            </a>
            <a href="{{ route('students.index') }}"
                class="{{ $currentRoute == 'students.index' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <i class="fas fa-users mr-2"></i> Siswa
            </a>
            <a href="{{ route('classrooms.index') }}"
                class="{{ $currentRoute == 'classrooms.index' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <i class="fas fa-door-open mr-2"></i> Kelas
            </a>
            <a href="{{ route('subjects.index') }}"
                class="{{ $currentRoute == 'subjects.index' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <i class="fas fa-book mr-2"></i> Mata Pelajaran
            </a>
            <a href="{{ route('admin.schedules.index') }}"
                class="{{ $currentRoute == 'admin.schedules.index' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <i class="fas fa-calendar mr-2"></i> Jadwal
            </a>
            <a href="{{ route('attendance.index') }}"
                class="{{ $currentRoute == 'attendance.index' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} block px-3 py-2 rounded-md text-base font-medium flex items-center">
                <i class="fas fa-clipboard-check mr-2"></i> Presensi
            </a>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full"
                        src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}"
                        alt="">
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                
                <a href="{{ route('admin.permissions') }}"
                    class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition duration-200 flex items-center">
                    <i class="fas fa-key mr-2"></i> Manajemen Izin
                </a>
                <a href="{{ route('logout') }}"
                    class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition duration-200 flex items-center"
                    onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Include AlpineJS for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    // Mobile menu toggle with animation
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        mobileMenuButton.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            
            // Toggle menu visibility with animation
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.style.maxHeight = '0';
                setTimeout(() => {
                    mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                }, 10);
            } else {
                mobileMenu.style.maxHeight = '0';
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
            }
            
            // Toggle icons
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
    });
</script>

<style>
    #mobile-menu {
        transition: max-height 0.3s ease-out;
        overflow: hidden;
        max-height: 0;
    }
    
    .nav-link {
        position: relative;
        transition: all 0.2s ease;
    }
    
    .nav-link:hover {
        transform: translateY(-2px);
    }
    
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 20px;
        height: 3px;
        background-color: #4f46e5;
        border-radius: 3px;
    }
</style>
