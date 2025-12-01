<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <!-- PENTING: Untuk ikon, kita tambahkan Font Awesome (Opsional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMD/CDQdM/w2sFqS" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 antialiased">
    <div class="flex h-screen">
        <!-- START: SIDEBAR -->
        <div class="w-64 bg-gray-800 text-white flex flex-col space-y-6 py-7 px-2">
            <a href="{{ route('dashboard') }}" class="text-white text-2xl font-extrabold uppercase p-2 border-b border-gray-700 flex items-center">
                <img src="{{ asset('images/bot.png') }}" 
                    alt="Logo Dinas" 
                    class="w-10 h-10 mr-4 object-cover mr-3">
                Bot Pengaduan 
            </a>

            <nav>
                <!-- Menu Dashboard -->
                <a href="{{ route('dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white font-semibold' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>

                @if(Auth::user()->role === 'superadmin')
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <p class="text-xs uppercase text-gray-400 px-4 mb-2">Manajemen & Laporan</p>
                    
                    <!-- Dropdown Laporan -->
                    <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }" class="relative">
                        <button @click="open = !open" class="flex justify-between items-center w-full py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white focus:outline-none">
                            <span><i class="fas fa-chart-bar mr-3"></i> Laporan Detail</span>
                            <i class="fas fa-chevron-down text-xs ml-2 transform" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open" x-cloak class="mt-1 ml-4 space-y-1">
                            <!-- Link 1: Data Pelapor Unik (PDF) -->
                            <a href="{{ route('reports.all') }}" class="block py-2 px-4 text-sm rounded transition duration-200 hover:bg-gray-600 {{ request()->routeIs('reports.all') ? 'bg-gray-600' : '' }}">
                                Data Laporan Pengaduan
                            </a>
                            <!-- Link 2: Laporan Kinerja Agen (Koreksi route name) -->
                            <a href="{{ route('admin.agen-dinas.index') }}" class="block py-2 px-4 text-sm rounded transition duration-200 hover:bg-gray-600 {{ request()->routeIs('reports.agents') ? 'bg-gray-600' : '' }}">
                                Data User Agen
                            </a>
                            <!-- Link 3: Laporan User Pelapor (Index) -->
                             <a href="{{ route('reports.users') }}" class="block py-2 px-4 text-sm rounded transition duration-200 hover:bg-gray-600 {{ request()->routeIs('reports.users') ? 'bg-gray-600' : '' }}">
                                Daftar Pelapor
                            </a>
                            </a>
                            <!-- Link 3: Laporan User Pelapor (Index) -->
                             <a href="{{ route('admin.categories.index') }}" class="block py-2 px-4 text-sm rounded transition duration-200 hover:bg-gray-600 {{ request()->routeIs('reports.users') ? 'bg-gray-600' : '' }}">
                                Kategori
                            </a>
                        </div>
                    </div>

                    <!-- Menu Manajemen Pegawai (Menu Utama) -->
                    <a href="{{ route('admin.pegawai.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.pegawai.*') ? 'bg-gray-700 text-white font-semibold' : '' }} mt-2">
                        <i class="fas fa-users-cog mr-3"></i> Manajemen Pegawai
                    </a>
                </div>
                @endif
                
                @if(auth()->user()->role === 'admin_dinas')
                <!-- Menu khusus Admin Dinas -->
                <li class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white ">
                    <a href="{{ route('admin_dinas.complaints.index') }}" class="flex items-center space-x-3">
                        <i class="bi bi-inbox"></i>
                        <span>Pengaduan</span>
                    </a>
                </li>

                <li class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white ">
                    <a href="{{ route('admin_dinas.categories.index') }}" class="flex items-center space-x-3">
                        <i class="bi bi-tags"></i>
                        <span>Kategori</span>
                    </a>
                </li>
                @endif

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button type="submit" class="block py-2.5 px-4 w-full text-left rounded transition duration-200 bg-red-600 hover:bg-red-700 text-white">
                        <i class="fas fa-sign-out-alt mr-3"></i> Keluar
                    </button>
                </form>
            </nav>
        </div>
        <!-- END: SIDEBAR -->
        
        <!-- START: CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center p-4 bg-white shadow-md">
                <h1 class="text-2xl font-bold text-gray-800">@yield('title')</h1>
                
                <!-- Profile Dropdown -->
                <div class="relative">
                    <!-- Trigger Button -->
                    <button 
                        id="profileDropdownBtn"
                        class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition duration-200"
                    >
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700">
                            {{ Auth::user()->name }}
                        </span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div 
                        id="profileDropdown"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 hidden"
                    >
                        <!-- User Info -->
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            <p class="text-xs text-blue-600 font-medium mt-1">
                                {{ ucfirst(Auth::user()->role) }}
                            </p>
                        </div>

                        <!-- Edit Profile Link -->
                        

                        <!-- Change Password Link -->
                        <a 
                            href="{{ route('profile.edit') }}#password" 
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition duration-150"
                        >
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Ubah Password
                        </a>

                        <!-- Divider -->
                        <div class="border-t border-gray-100 my-1"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
        <!-- END: CONTENT -->
        
    </div>
    
    <!-- PENTING: Kita tambahkan Alpine.js untuk fungsionalitas Dropdown Sidebar -->
    <script src="//unpkg.com/alpinejs" defer></script> 
</body>
<!-- Simple JavaScript for Dropdown -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.getElementById('profileDropdownBtn');
        const dropdownMenu = document.getElementById('profileDropdown');
        
        // Toggle dropdown
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.classList.add('hidden');
        });
        
        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Close dropdown when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.add('hidden');
            }
        });
    });
</script>
</html>