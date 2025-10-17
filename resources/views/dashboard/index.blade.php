@extends('layouts.app')

@section('title', 'Dashboard - OrindPOS')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Penjualan Hari Ini -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Penjualan Hari Ini</p>
                    <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                    <div class="mt-3 flex items-center gap-1">
                        @if($salesGrowth >= 0)
                            <i class="fas fa-arrow-up text-xs"></i>
                            <span class="text-xs">+{{ number_format($salesGrowth, 1) }}%</span>
                        @else
                            <i class="fas fa-arrow-down text-xs"></i>
                            <span class="text-xs">{{ number_format($salesGrowth, 1) }}%</span>
                        @endif
                        <span class="text-xs text-blue-100 ml-1">vs kemarin</span>
                    </div>
                </div>
                <div class="bg-white/20 p-4 rounded-lg">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Transaksi -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Transaksi</p>
                    <h3 class="text-3xl font-bold mt-2">{{ $todayTransactions }}</h3>
                    <div class="mt-3 flex items-center gap-1">
                        @if($transactionGrowth >= 0)
                            <i class="fas fa-arrow-up text-xs"></i>
                            <span class="text-xs">+{{ number_format($transactionGrowth, 1) }}%</span>
                        @else
                            <i class="fas fa-arrow-down text-xs"></i>
                            <span class="text-xs">{{ number_format($transactionGrowth, 1) }}%</span>
                        @endif
                        <span class="text-xs text-green-100 ml-1">vs kemarin</span>
                    </div>
                </div>
                <div class="bg-white/20 p-4 rounded-lg">
                    <i class="fas fa-shopping-cart text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Rata-rata Transaksi -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Rata-rata Transaksi</p>
                    <h3 class="text-3xl font-bold mt-2">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</h3>
                    <p class="text-xs text-purple-100 mt-3">Per transaksi hari ini</p>
                </div>
                <div class="bg-white/20 p-4 rounded-lg">
                    <i class="fas fa-calculator text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Stok Menipis -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Stok Menipis</p>
                    <h3 class="text-3xl font-bold mt-2">{{ $lowStock }}</h3>
                    <p class="text-xs text-orange-100 mt-3">Produk perlu restock</p>
                </div>
                <div class="bg-white/20 p-4 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-500"></i>
            Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('transaksi.index') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all group">
                <div class="bg-blue-500 p-3 rounded-lg mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-cash-register text-white text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Transaksi Baru</span>
            </a>
            <a href="{{ route('barang.index') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all group">
                <div class="bg-green-500 p-3 rounded-lg mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-box text-white text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Data Barang</span>
            </a>
            <a href="{{ route('laporan.harian') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all group">
                <div class="bg-purple-500 p-3 rounded-lg mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-invoice text-white text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Laporan</span>
            </a>
            <a href="{{ route('user.index') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg hover:from-orange-100 hover:to-orange-200 transition-all group">
                <div class="bg-orange-500 p-3 rounded-lg mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Manajemen User</span>
            </a>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Sales Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-area text-blue-500"></i>
                Grafik Penjualan 7 Hari Terakhir
            </h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-500"></i>
                Produk Terlaris (30 Hari)
            </h3>
            @if($topProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($topProducts as $index => $product)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $product->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $product->total_terjual }} terjual</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full" style="width: {{ ($product->total_terjual / $topProducts->first()->total_terjual) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-2"></i>
                    <p>Belum ada data penjualan</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($lowStockItems->count() > 0)
    <div class="bg-gradient-to-r from-orange-50 to-red-50 border-l-4 border-orange-500 rounded-lg shadow-lg p-6">
        <div class="flex items-start gap-3">
            <div class="bg-orange-500 p-2 rounded-lg">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-800 mb-3">⚠️ Peringatan Stok Menipis!</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($lowStockItems as $item)
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $item->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $item->kategori->nama ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-orange-600">{{ $item->stok }}</p>
                                <p class="text-xs text-gray-500">stok</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('barang.index') }}" class="inline-block mt-4 text-sm text-orange-600 hover:text-orange-700 font-medium">
                    Lihat semua produk stok menipis <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Produk</p>
                    <h4 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalProducts }}</h4>
                </div>
                <i class="fas fa-boxes text-blue-500 text-3xl opacity-20"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Kategori</p>
                    <h4 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalCategories }}</h4>
                </div>
                <i class="fas fa-tags text-green-500 text-3xl opacity-20"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total User</p>
                    <h4 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalUsers }}</h4>
                </div>
                <i class="fas fa-user-friends text-purple-500 text-3xl opacity-20"></i>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        const salesData = @json($salesChart);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(item => item.date),
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: salesData.map(item => item.sales),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
