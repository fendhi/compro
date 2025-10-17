<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OrindPOS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- OrindPOS Universal Notifications -->
    <script src="{{ asset('js/orind-notifications.js') }}"></script>
    
    @stack('styles')
    <style>
        .sidebar-collapsed {
            width: 5rem;
        }
        .sidebar-expanded {
            width: 16rem;
        }
        .sidebar-transition {
            transition: width 0.3s ease-in-out;
        }
        .menu-text {
            transition: opacity 0.2s ease-in-out;
        }
        
        /* Custom Scrollbar untuk Sidebar - Modern & Clean */
        aside::-webkit-scrollbar,
        aside nav::-webkit-scrollbar {
            width: 6px;
        }
        
        aside::-webkit-scrollbar-track,
        aside nav::-webkit-scrollbar-track {
            background: transparent;
        }
        
        aside::-webkit-scrollbar-thumb,
        aside nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        
        aside:hover::-webkit-scrollbar-thumb,
        aside nav:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
        }
        
        aside::-webkit-scrollbar-thumb:hover,
        aside nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Firefox */
        aside,
        aside nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

    <!-- Container utama -->
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar - Modern Design -->
        <aside id="sidebar" class="sidebar-expanded sidebar-transition bg-gradient-to-b from-[#00718F] to-[#005670] text-white flex flex-col shadow-xl relative">
            
            <!-- Logo & Brand -->
            <div class="p-5 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-cash-register text-2xl"></i>
                    </div>
                    <span class="menu-text text-xl font-bold whitespace-nowrap">OrindPOS</span>
                </div>
                <button onclick="toggleSidebar()" class="text-white hover:bg-white/10 p-2 rounded-lg transition-colors">
                    <i id="toggleIcon" class="fas fa-angles-left"></i>
                </button>
            </div>

            <!-- Divider -->
            <div class="mx-4 h-px bg-white/20"></div>

            <!-- Navigation Menu -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('dashboard*') ? 'bg-white text-[#00718F] shadow-lg' : '' }}">
                    <i class="fas fa-home text-lg w-5"></i>
                    <span class="menu-text whitespace-nowrap">Dashboard</span>
                </a>

                <!-- Transaksi -->
                <a href="{{ route('transaksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('transaksi*') ? 'bg-white text-[#00718F] shadow-lg' : '' }}">
                    <i class="fas fa-shopping-cart text-lg w-5"></i>
                    <span class="menu-text whitespace-nowrap">Transaksi</span>
                </a>

                <!-- Master Data Dropdown - All roles (READ ONLY untuk Kasir) -->
                <div>
                    <button onclick="toggleDropdown('masterDataDropdown')" class="flex justify-between items-center w-full px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('barang*') || request()->routeIs('kategori*') ? 'bg-white/20' : '' }}">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-database text-lg w-5"></i>
                            <span class="menu-text whitespace-nowrap">Master Data</span>
                            @if(Auth::user()->isKasir())
                                <span class="menu-text text-xs bg-white/20 px-2 py-0.5 rounded">View</span>
                            @endif
                        </span>
                        <i id="masterDataArrow" class="menu-text fas fa-chevron-down text-sm transition-transform"></i>
                    </button>
                    <div id="masterDataDropdown" class="hidden mt-2 ml-12 space-y-1">
                        <a href="{{ route('barang.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm {{ request()->routeIs('barang*') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-box text-xs w-4"></i>
                            <span class="menu-text">Barang</span>
                        </a>
                        <a href="{{ route('kategori.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm {{ request()->routeIs('kategori*') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-tags text-xs w-4"></i>
                            <span class="menu-text">Kategori</span>
                        </a>
                    </div>
                </div>

                <!-- Laporan Dropdown - All roles (Kasir: hanya Harian) -->
                <div>
                    <button onclick="toggleDropdown('laporanDropdown')" class="flex justify-between items-center w-full px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('laporan*') ? 'bg-white/20' : '' }}">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-chart-line text-lg w-5"></i>
                            <span class="menu-text whitespace-nowrap">Laporan</span>
                        </span>
                        <i id="laporanArrow" class="menu-text fas fa-chevron-down text-sm transition-transform"></i>
                    </button>
                    <div id="laporanDropdown" class="hidden mt-2 ml-12 space-y-1">
                        <a href="{{ route('laporan.harian') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm">
                            <i class="fas fa-calendar-day text-xs w-4"></i>
                            <span class="menu-text">Harian</span>
                        </a>
                        @if(Auth::user()->hasRole('owner', 'admin'))
                        <a href="{{ route('laporan.bulanan') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm">
                            <i class="fas fa-calendar-alt text-xs w-4"></i>
                            <span class="menu-text">Bulanan</span>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Inventory - All roles (READ ONLY untuk Kasir) -->
                <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('inventory*') ? 'bg-white text-[#00718F] shadow-lg' : '' }}">
                    <i class="fas fa-warehouse text-lg w-5"></i>
                    <span class="menu-text whitespace-nowrap">Inventory</span>
                    @if(Auth::user()->isKasir())
                        <span class="menu-text text-xs bg-white/20 px-2 py-0.5 rounded">View</span>
                    @endif
                </a>

                <!-- Audit Trail - OWNER & ADMIN ONLY -->
                @if(Auth::user()->hasRole('owner', 'admin'))
                <a href="{{ route('audit.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('audit*') ? 'bg-white text-[#00718F] shadow-lg' : '' }}">
                    <i class="fas fa-history text-lg w-5"></i>
                    <span class="menu-text whitespace-nowrap">Audit Trail</span>
                </a>
                @endif

                <!-- Keuangan Dropdown - OWNER & ADMIN ONLY -->
                @if(Auth::user()->canAccessFinancial())
                <div>
                    <button onclick="toggleDropdown('keuanganDropdown')" class="flex justify-between items-center w-full px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('keuangan*') || request()->routeIs('pengeluaran*') ? 'bg-white/20' : '' }}">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-money-bill-wave text-lg w-5"></i>
                            <span class="menu-text whitespace-nowrap">Keuangan</span>
                        </span>
                        <i id="keuanganArrow" class="menu-text fas fa-chevron-down text-sm transition-transform"></i>
                    </button>
                    <div id="keuanganDropdown" class="hidden mt-2 ml-12 space-y-1">
                        <a href="{{ route('keuangan.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm {{ request()->routeIs('keuangan.dashboard') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-chart-pie text-xs w-4"></i>
                            <span class="menu-text">Financial Dashboard</span>
                        </a>
                        <a href="{{ route('pengeluaran.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm {{ request()->routeIs('pengeluaran*') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-wallet text-xs w-4"></i>
                            <span class="menu-text">Data Pengeluaran</span>
                        </a>
                        <a href="{{ route('keuangan.laba-rugi') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm {{ request()->routeIs('keuangan.laba-rugi') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-file-invoice-dollar text-xs w-4"></i>
                            <span class="menu-text">Laporan Laba Rugi</span>
                        </a>
                        <a href="{{ route('keuangan.arus-kas') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm {{ request()->routeIs('keuangan.arus-kas') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-dollar-sign text-xs w-4"></i>
                            <span class="menu-text">Laporan Arus Kas</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Management Dropdown - OWNER ONLY -->
                @if(Auth::user()->canManageUsers())
                <div>
                    <button onclick="toggleDropdown('managementDropdown')" class="flex justify-between items-center w-full px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('user*') ? 'bg-white/20' : '' }}">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-cog text-lg w-5"></i>
                            <span class="menu-text whitespace-nowrap">Management</span>
                        </span>
                        <i id="managementArrow" class="menu-text fas fa-chevron-down text-sm transition-transform"></i>
                    </button>
                    <div id="managementDropdown" class="hidden mt-2 ml-12 space-y-1">
                        <a href="{{ route('user.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-white/10 transition-all text-sm {{ request()->routeIs('user*') ? 'bg-white/20' : '' }}">
                            <i class="fas fa-users text-xs w-4"></i>
                            <span class="menu-text">Manajemen User</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>

            <!-- User Info Footer -->
            <div class="p-4 border-t border-white/20">
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-white/10">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div class="menu-text flex-1 overflow-hidden">
                        <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-white/70 truncate">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="mainContent" class="flex-1 bg-gray-50 overflow-hidden flex flex-col transition-all">
            <!-- Header -->
            <header class="bg-white px-8 py-4 shadow-sm border-b flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden text-[#00718F] hover:bg-gray-100 p-2 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-[#00718F]">@yield('header', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-2 text-gray-600">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-2 rounded-lg hover:from-red-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Konten utama -->
            <section class="flex-1 p-8 overflow-y-auto">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg mb-6 shadow-sm flex items-start gap-3">
                        <i class="fas fa-check-circle text-xl mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6 shadow-sm flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-xl mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Error!</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <!-- Overlay untuk mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <script>
        let sidebarCollapsed = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggleIcon');
            const menuTexts = document.querySelectorAll('.menu-text');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebarCollapsed = !sidebarCollapsed;
            
            if (sidebarCollapsed) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                toggleIcon.classList.remove('fa-angles-left');
                toggleIcon.classList.add('fa-angles-right');
                
                menuTexts.forEach(text => {
                    text.style.opacity = '0';
                    setTimeout(() => text.style.display = 'none', 200);
                });
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                toggleIcon.classList.remove('fa-angles-right');
                toggleIcon.classList.add('fa-angles-left');
                
                menuTexts.forEach(text => {
                    text.style.display = '';
                    setTimeout(() => text.style.opacity = '1', 50);
                });
            }
            
            // Mobile: Toggle overlay
            if (window.innerWidth < 1024) {
                overlay.classList.toggle('hidden');
            }
        }

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const arrowId = dropdownId.replace('Dropdown', 'Arrow');
            const arrow = document.getElementById(arrowId);
            
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                dropdown.classList.add('block');
                if (arrow) {
                    arrow.style.transform = 'rotate(180deg)';
                }
            } else {
                dropdown.classList.remove('block');
                dropdown.classList.add('hidden');
                if (arrow) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            }
        }

        // Auto-open active dropdowns on page load
        document.addEventListener('DOMContentLoaded', function() {
            const activeLinks = document.querySelectorAll('a.bg-white\\/20');
            activeLinks.forEach(link => {
                const dropdown = link.closest('[id$="Dropdown"]');
                if (dropdown && dropdown.classList.contains('hidden')) {
                    const dropdownId = dropdown.id;
                    toggleDropdown(dropdownId);
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
