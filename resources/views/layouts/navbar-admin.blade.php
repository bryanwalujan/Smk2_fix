@php
    $currentRoute = Route::currentRouteName();
@endphp

<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo Sekolah">
                    <span class="ml-2 text-xl font-bold text-gray-800">SMKN 3 TONDANO</span>
                </div>
                <!-- Navigation Links -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ $currentRoute == 'admin.dashboard' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('teachers.index') }}"
                        class="{{ $currentRoute == 'teachers.index' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-chalkboard-teacher mr-2"></i> Guru
                    </a>
                    <a href="{{ route('students.index') }}"
                        class="{{ $currentRoute == 'students.index' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-users mr-2"></i> Siswa
                    </a>
                    <a href="{{ route('classrooms.index') }}"
                        class="{{ $currentRoute == 'classrooms.index' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-door-open mr-2"></i> Kelas
                        <a href="{{ route('admin.schedules.index') }}" class="text-blue-600 hover:underline">Lihat Jadwal Kelas</a>
                    </a>
                    <a href="{{ route('attendance.index') }}"
                        class="{{ $currentRoute == 'attendance.index' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-clipboard-check mr-2"></i> Presensi
                    </a>
                </div>
            </div>
            <!-- Profile dropdown -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <div class="ml-3 relative">
                    <div>
                        <button type="button"
                            class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            id="user-menu" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <img class="h-8 w-8 rounded-full"
                                src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}"
                                alt="">
                        </button>
                    </div>
                </div>
                <div class="ml-3 relative">
                    <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="ml-4 text-sm text-gray-500 hover:text-gray-700"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                class="{{ $currentRoute == 'admin.dashboard' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="{{ route('teachers.index') }}"
                class="{{ $currentRoute == 'teachers.index' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Guru
            </a>
            <a href="{{ route('students.index') }}"
                class="{{ $currentRoute == 'students.index' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                <i class="fas fa-users mr-2"></i> Siswa
            </a>
            <a href="{{ route('classrooms.index') }}"
                class="{{ $currentRoute == 'classrooms.index' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                <i class="fas fa-door-open mr-2"></i> Kelas
            </a>
            <a href="{{ route('attendance.index') }}"
                class="{{ $currentRoute == 'attendance.index' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
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
                <a href="#"
                    class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                    <i class="fas fa-user-circle mr-2"></i> Profil Saya
                </a>
                <a href="{{ route('logout') }}"
                    class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
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

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', function () {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>