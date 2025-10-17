@extends('layouts.app')

@section('header', 'Financial Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Filter Periode -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="GET" action="{{ route('keuangan.dashboard') }}" id="filterForm" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Periode
                    <span class="text-xs text-gray-500 ml-1">(auto-filter)</span>
                </label>
                <select name="period" id="periodFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            
            <div class="flex-1" id="customDateRange" style="display: {{ $period == 'custom' ? 'block' : 'none' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" id="startDate" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F]">
            </div>
            
            <div class="flex-1" id="customDateRangeEnd" style="display: {{ $period == 'custom' ? 'block' : 'none' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" id="endDate" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F]">
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('keuangan.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors" title="Reset Filter">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Pemasukan Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">💰 Penjualan</h3>
                <i class="fas fa-arrow-up text-xl opacity-75"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">{{ 'Rp ' . number_format($pemasukan, 0, ',', '.') }}</h2>
            <p class="text-xs opacity-90">
                <i class="fas fa-shopping-cart mr-1"></i>{{ $jumlahTransaksi }} transaksi
            </p>
        </div>

        <!-- HPP Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">📦 HPP</h3>
                <i class="fas fa-boxes text-xl opacity-75"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">{{ 'Rp ' . number_format($hpp, 0, ',', '.') }}</h2>
            <p class="text-xs opacity-90">
                <i class="fas fa-info-circle mr-1"></i>Harga Pokok
            </p>
        </div>

        <!-- Laba Kotor Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">� Laba Kotor</h3>
                <i class="fas fa-hand-holding-usd text-xl opacity-75"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">{{ 'Rp ' . number_format($labaKotor, 0, ',', '.') }}</h2>
            <p class="text-xs opacity-90">
                <i class="fas fa-percentage mr-1"></i>{{ $pemasukan > 0 ? number_format(($labaKotor / $pemasukan) * 100, 1) : 0 }}% margin
            </p>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">💸 Biaya Ops</h3>
                <i class="fas fa-arrow-down text-xl opacity-75"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">{{ 'Rp ' . number_format($pengeluaran, 0, ',', '.') }}</h2>
            <p class="text-xs opacity-90">
                <a href="{{ route('pengeluaran.index') }}" class="hover:underline">
                    <i class="fas fa-list mr-1"></i>Lihat detail
                </a>
            </p>
        </div>

        <!-- Profit Bersih Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">📈 Laba Bersih</h3>
                <i class="fas fa-chart-line text-xl opacity-75"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">{{ 'Rp ' . number_format($profitBersih, 0, ',', '.') }}</h2>
            <p class="text-xs opacity-90">
                <i class="fas fa-calculator mr-1"></i>Avg: Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart: Penjualan vs Pengeluaran -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line mr-2 text-[#00718F]"></i>
                Penjualan vs Pengeluaran (7 Hari Terakhir)
            </h3>
            <div style="height: 300px;">
                <canvas id="salesExpenseChart"></canvas>
            </div>
        </div>

        <!-- Top 5 Pengeluaran -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-list-ol mr-2 text-[#00718F]"></i>
                Top 5 Pengeluaran
            </h3>
            <div class="space-y-3">
                @forelse($topExpenses as $index => $expense)
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#00718F] text-white flex items-center justify-center font-bold text-sm">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $expense->deskripsi }}</p>
                        <p class="text-xs text-gray-500">{{ $expense->kategori->nama }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-red-600">{{ 'Rp ' . number_format($expense->nominal, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $expense->tanggal->format('d/m/Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data pengeluaran</p>
                </div>
                @endforelse
            </div>
            
            @if($topExpenses->count() > 0)
            <a href="{{ route('pengeluaran.index') }}" class="block mt-4 text-center text-sm text-[#00718F] hover:underline font-medium">
                Lihat Semua Pengeluaran <i class="fas fa-arrow-right ml-1"></i>
            </a>
            @endif
        </div>
    </div>

    <!-- Expense Breakdown (Pie Chart) -->
    @if($expenseBreakdown->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie mr-2 text-[#00718F]"></i>
            Breakdown Pengeluaran by Kategori
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div style="height: 300px;">
                <canvas id="expenseBreakdownChart"></canvas>
            </div>
            <div class="space-y-2">
                @foreach($expenseBreakdown as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded" style="background-color: {{ $item['warna'] }}"></div>
                        <span class="text-sm font-medium text-gray-700">{{ $item['kategori'] }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-800">{{ 'Rp ' . number_format($item['total'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">
                            {{ number_format(($item['total'] / $pengeluaran) * 100, 1) }}%
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt mr-2 text-[#00718F]"></i>
            Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('pengeluaran.create') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-[#00718F] to-[#005670] text-white rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-plus-circle text-3xl mb-2"></i>
                <span class="text-sm font-medium">Tambah Pengeluaran</span>
            </a>
            <a href="{{ route('keuangan.laba-rugi') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-file-invoice-dollar text-3xl mb-2"></i>
                <span class="text-sm font-medium">Laporan Laba Rugi</span>
            </a>
            <a href="{{ route('keuangan.arus-kas') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-dollar-sign text-3xl mb-2"></i>
                <span class="text-sm font-medium">Laporan Arus Kas</span>
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-list text-3xl mb-2"></i>
                <span class="text-sm font-medium">List Pengeluaran</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Auto-submit filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const periodFilter = document.getElementById('periodFilter');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const customDateRange = document.getElementById('customDateRange');
    const customDateRangeEnd = document.getElementById('customDateRangeEnd');
    
    // Auto-submit when period changes
    periodFilter.addEventListener('change', function() {
        const selectedPeriod = this.value;
        
        // Show/hide custom date inputs
        if (selectedPeriod === 'custom') {
            customDateRange.style.display = 'block';
            customDateRangeEnd.style.display = 'block';
            // Don't auto-submit for custom, wait for dates
        } else {
            customDateRange.style.display = 'none';
            customDateRangeEnd.style.display = 'none';
            // Auto-submit for predefined periods
            form.submit();
        }
    });
    
    // Auto-submit when custom dates change (with debounce)
    let dateChangeTimeout;
    function handleDateChange() {
        clearTimeout(dateChangeTimeout);
        
        // Only submit if both dates are filled
        if (periodFilter.value === 'custom' && startDate.value && endDate.value) {
            dateChangeTimeout = setTimeout(() => {
                form.submit();
            }, 500); // Wait 500ms after last change
        }
    }
    
    startDate.addEventListener('change', handleDateChange);
    endDate.addEventListener('change', handleDateChange);
});

// Period filter toggle custom date
document.getElementById('periodFilter').addEventListener('change', function() {
    const customRange = document.getElementById('customDateRange');
    const customRangeEnd = document.getElementById('customDateRangeEnd');
    if (this.value === 'custom') {
        customRange.style.display = 'block';
        customRangeEnd.style.display = 'block';
    } else {
        customRange.style.display = 'none';
        customRangeEnd.style.display = 'none';
    }
});

// Chart Data
const chartData = @json($chartData);

// Sales vs Expense Chart (Line Chart)
const ctx = document.getElementById('salesExpenseChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: chartData.map(d => d.pemasukan),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Pengeluaran',
                    data: chartData.map(d => d.pengeluaran),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Profit',
                    data: chartData.map(d => d.profit),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
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

// Expense Breakdown Chart (Pie Chart)
@if($expenseBreakdown->count() > 0)
const breakdownCtx = document.getElementById('expenseBreakdownChart');
if (breakdownCtx) {
    const breakdownData = @json($expenseBreakdown);
    
    new Chart(breakdownCtx, {
        type: 'doughnut',
        data: {
            labels: breakdownData.map(d => d.kategori),
            datasets: [{
                data: breakdownData.map(d => d.total),
                backgroundColor: breakdownData.map(d => d.warna),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID') + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
@endif
</script>
@endpush
