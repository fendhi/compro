<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'OrindPOS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">

    <!-- Container utama -->
    <div class="flex h-screen">

        <!-- Sidebar -->
        <aside class="w-64 text-white flex flex-col border-r-2 border-white" style="background-color: #00718F;">
            <div class="p-5 text-2xl font-bold border-b-2 border-white flex items-center justify-center">
                <img src="{{ asset('logo.png') }}" alt="OrindPOS Logo" class="h-12 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span style="display:none;">OrindPOS</span>
            </div>

            <nav class="flex-1 p-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 p-3 rounded-md border border-white/30 hover:bg-white hover:text-gray-800 transition-colors {{ request()->routeIs('dashboard*') ? 'bg-white text-gray-800' : '' }}" style="{{ request()->routeIs('dashboard*') ? '' : 'background-color: rgba(0, 113, 143, 0.5);' }}">
                    <span>🏠</span> <span>Dashboard</span>
                </a>

                <a href="{{ route('transaksi.index') }}" class="flex items-center gap-2 p-3 rounded-md border border-white/30 hover:bg-white hover:text-gray-800 transition-colors {{ request()->routeIs('transaksi*') ? 'bg-white text-gray-800' : '' }}" style="{{ request()->routeIs('transaksi*') ? '' : 'background-color: rgba(0, 113, 143, 0.5);' }}">
                    <span>💳</span> <span>Transaksi</span>
                </a>

                <div class="border border-white/30 rounded-md">
                    <button onclick="toggleDropdown('masterDataDropdown')" class="flex justify-between items-center w-full p-3 rounded-md hover:bg-white hover:text-gray-800 transition-colors" style="background-color: rgba(0, 113, 143, 0.5);">
                        <span class="flex items-center gap-2">
                            <span>🗃️</span> Master Data
                        </span>
                        <span id="masterDataArrow">▾</span>
                    </button>
                    <div id="masterDataDropdown" class="hidden pl-10 flex-col gap-1 text-sm mt-1 pb-2">
                        <a href="{{ route('barang.index') }}" class="inline-block w-fit py-0.5 px-1.5 rounded hover:bg-white hover:text-gray-800 transition-colors">Barang</a>
                        <a href="{{ route('kategori.index') }}" class="inline-block w-fit py-0.5 px-1.5 rounded hover:bg-white hover:text-gray-800 transition-colors">Kategori</a>
                    </div>
                </div>

                <div class="border border-white/30 rounded-md">
                    <button onclick="toggleDropdown('laporanDropdown')" class="flex justify-between items-center w-full p-3 rounded-md hover:bg-white hover:text-gray-800 transition-colors" style="background-color: rgba(0, 113, 143, 0.5);">
                        <span class="flex items-center gap-2">
                            <span>📊</span> Laporan
                        </span>
                        <span id="laporanArrow">▾</span>
                    </button>
                    <div id="laporanDropdown" class="hidden pl-10 flex-col gap-1 text-sm mt-1 pb-2">
                        <a href="{{ route('laporan.harian') }}" class="inline-block w-fit py-0.5 px-1.5 rounded hover:bg-white hover:text-gray-800 transition-colors">Laporan Harian</a>
                        <a href="{{ route('laporan.bulanan') }}" class="inline-block w-fit py-0.5 px-1.5 rounded hover:bg-white hover:text-gray-800 transition-colors">Laporan Bulanan</a>
                    </div>
                </div>

                <div class="border border-white/30 rounded-md">
                    <button onclick="toggleDropdown('managementDropdown')" class="flex justify-between items-center w-full p-3 rounded-md hover:bg-white hover:text-gray-800 transition-colors" style="background-color: rgba(0, 113, 143, 0.5);">
                        <span class="flex items-center gap-2">
                            <span>⚙️</span> Management
                        </span>
                        <span id="managementArrow">▾</span>
                    </button>
                    <div id="managementDropdown" class="hidden pl-10 flex-col gap-1 text-sm mt-1 pb-2">
                        <a href="{{ route('user.index') }}" class="inline-block w-fit py-0.5 px-1.5 rounded hover:bg-white hover:text-gray-800 transition-colors">Manajemen User</a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 bg-gray-100">
            <!-- Header -->
            <header class="flex justify-between items-center bg-gray-50 px-8 py-4 shadow-sm border-b-2 border-white">
                <h1 class="text-xl font-semibold" style="color: #00718F;">@yield('header', 'OrindPOS')</h1>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-white px-4 py-2 rounded-md hover:opacity-90 border-2 border-white" style="background-color: #00718F;">Logout</button>
                </form>
            </header>

            <!-- Konten utama -->
            <section class="p-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <script>
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                dropdown.classList.add('flex');
            } else {
                dropdown.classList.remove('flex');
                dropdown.classList.add('hidden');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
