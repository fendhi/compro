@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('header', 'Keuangan - Laporan Laba Rugi')

@section('content')

<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Laba Rugi</h1>
        <p class="text-gray-600 text-sm mt-1">Income Statement - Analisis profit & loss bisnis Anda</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('keuangan.laba-rugi') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Periode Preset -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Periode
                    <span class="text-xs text-gray-500">(auto-filter)</span>
                </label>
                <select 
                    name="period" 
                    id="periodSelect"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div id="customDateRange" class="{{ $period == 'custom' ? '' : 'hidden' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input 
                    type="date" 
                    name="start_date"
                    id="startDate" 
                    value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
            </div>

            <!-- Tanggal Akhir -->
            <div id="customDateRange2" class="{{ $period == 'custom' ? '' : 'hidden' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Akhir</label>
                <input 
                    type="date"
                    name="end_date"
                    id="endDate" 
                    value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <a href="{{ route('keuangan.laba-rugi') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition" title="Reset Filter">
                    <i class="fas fa-redo"></i>
                </a>
                <a href="{{ route('keuangan.laba-rugi.pdf', request()->all()) }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" 
                   title="Export PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Period Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
            <div>
                <h3 class="font-semibold text-blue-800">Periode Laporan</h3>
                <p class="text-sm text-blue-700">
                    {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                    <span class="ml-2 text-xs">({{ \Carbon\Carbon::parse($startDate)->diffInDays($endDate) + 1 }} hari)</span>
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Laporan Laba Rugi Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden" id="printable-area">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-6 text-white">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold mb-1">LAPORAN LABA RUGI</h2>
                        <p class="text-sm opacity-90">{{ config('app.name', 'OrindPOS Vapor') }}</p>
                        <p class="text-xs opacity-75 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    
                    <!-- PENDAPATAN -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4">PENDAPATAN (REVENUE)</h3>
                        <div class="flex justify-between items-center pl-4 hover:bg-gray-50 py-2 rounded">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-shopping-cart text-blue-500"></i>
                                <span class="text-gray-700">Penjualan</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ 'Rp ' . number_format($penjualan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-300">

                    <!-- HARGA POKOK PENJUALAN -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4">HARGA POKOK PENJUALAN (COGS)</h3>
                        <div class="flex justify-between items-center pl-4 hover:bg-gray-50 py-2 rounded">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-boxes text-orange-500"></i>
                                <span class="text-gray-700">HPP</span>
                            </div>
                            <span class="font-semibold text-red-600">{{ 'Rp ' . number_format($hpp, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-300">

                    <!-- LABA KOTOR -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <span class="font-bold text-gray-800 text-lg">LABA KOTOR (Gross Profit)</span>
                            <span class="font-bold text-green-600 text-xl">{{ 'Rp ' . number_format($labaKotor, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-right">Margin: {{ number_format($grossMargin, 2) }}%</p>
                    </div>

                    <hr class="my-6 border-gray-300 border-2">

                    <!-- BEBAN OPERASIONAL -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4">BEBAN OPERASIONAL (Operating Expenses)</h3>
                        <div class="space-y-2">
                            @forelse($expensesByCategory as $category)
                            <div class="flex justify-between items-center pl-4 hover:bg-gray-50 py-2 rounded">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->warna ?? '#6B7280' }}"></span>
                                    <span class="text-gray-700">{{ $category->nama }}</span>
                                </div>
                                <span class="font-semibold text-red-600">{{ 'Rp ' . number_format($category->total_expense, 0, ',', '.') }}</span>
                            </div>
                            @empty
                            <div class="text-center text-gray-400 py-4">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="text-sm">Tidak ada beban operasional</p>
                            </div>
                            @endforelse
                            
                            @if(count($expensesByCategory) > 0)
                            <div class="flex justify-between items-center bg-orange-50 p-3 rounded-lg border-l-4 border-orange-500 mt-3">
                                <span class="font-bold text-gray-800">Total Beban Operasional</span>
                                <span class="font-bold text-orange-600 text-lg">{{ 'Rp ' . number_format($totalExpenses, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-6 border-gray-300 border-2">

                    <!-- LABA BERSIH -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center bg-gradient-to-r from-{{ $labaBersih >= 0 ? 'green' : 'red' }}-500 to-{{ $labaBersih >= 0 ? 'green' : 'red' }}-600 p-6 rounded-lg shadow-lg text-white">
                            <div>
                                <span class="text-sm opacity-90">{{ $labaBersih >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</span>
                                <h2 class="font-bold text-3xl mt-1">{{ 'Rp ' . number_format(abs($labaBersih), 0, ',', '.') }}</h2>
                                <p class="text-xs opacity-75 mt-2">Laba Kotor - Beban Operasional</p>
                            </div>
                            <div class="text-right">
                                @if($labaBersih >= 0)
                                    <i class="fas fa-arrow-up text-4xl opacity-75"></i>
                                    <p class="text-xs mt-2 opacity-90">Profit</p>
                                @else
                                    <i class="fas fa-arrow-down text-4xl opacity-75"></i>
                                    <p class="text-xs mt-2 opacity-90">Loss</p>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-right">Profit Margin: {{ number_format($profitMargin, 2) }}%</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="space-y-4">
            
            <!-- Profit Margin Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold opacity-90">📈 Profit Margin</h3>
                    <i class="fas fa-percentage text-2xl opacity-75"></i>
                </div>
                <h2 class="text-3xl font-bold mb-2">{{ number_format($profitMargin, 2) }}%</h2>
                <p class="text-xs opacity-90">Net Profit / Revenue</p>
                <div class="mt-3 pt-3 border-t border-green-400">
                    @if($profitMargin >= 20)
                        <span class="text-xs font-semibold">✓ Sangat Baik</span>
                    @elseif($profitMargin >= 10)
                        <span class="text-xs font-semibold">⚠ Cukup Baik</span>
                    @else
                        <span class="text-xs font-semibold">⚠ Perlu Ditingkatkan</span>
                    @endif
                </div>
            </div>

            <!-- Gross Margin Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold opacity-90">💰 Gross Margin</h3>
                    <i class="fas fa-chart-line text-2xl opacity-75"></i>
                </div>
                <h2 class="text-3xl font-bold mb-2">{{ number_format($grossMargin, 2) }}%</h2>
                <p class="text-xs opacity-90">Gross Profit / Revenue</p>
                <div class="mt-3 pt-3 border-t border-blue-400">
                    @if($grossMargin >= 30)
                        <span class="text-xs font-semibold">✓ Sangat Baik</span>
                    @elseif($grossMargin >= 20)
                        <span class="text-xs font-semibold">⚠ Cukup Baik</span>
                    @else
                        <span class="text-xs font-semibold">⚠ Perlu Ditingkatkan</span>
                    @endif
                </div>
            </div>

            <!-- Operating Ratio Card -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold opacity-90">⚙️ Operating Ratio</h3>
                    <i class="fas fa-cogs text-2xl opacity-75"></i>
                </div>
                <h2 class="text-3xl font-bold mb-2">{{ number_format($operatingRatio, 2) }}%</h2>
                <p class="text-xs opacity-90">Operating Cost / Revenue</p>
                <div class="mt-3 pt-3 border-t border-orange-400">
                    @if($operatingRatio <= 70)
                        <span class="text-xs font-semibold">✓ Sangat Efisien</span>
                    @elseif($operatingRatio <= 85)
                        <span class="text-xs font-semibold">⚠ Cukup Efisien</span>
                    @else
                        <span class="text-xs font-semibold">⚠ Kurang Efisien</span>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Insight & Rekomendasi -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-blue-800 flex items-center gap-2 mb-4">
            <i class="fas fa-lightbulb"></i>
            Insight & Rekomendasi
        </h3>
        <div class="space-y-3 text-sm text-blue-900">
            @if($labaBersih < 0)
                <div class="flex items-start gap-3 bg-red-50 p-3 rounded-lg border-l-4 border-red-500">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-red-800">Bisnis mengalami kerugian</p>
                        <p class="text-red-700 text-xs mt-1">Perlu evaluasi harga jual, kontrol biaya, atau meningkatkan volume penjualan.</p>
                    </div>
                </div>
            @endif
            
            @if($grossMargin < 20)
                <div class="flex items-start gap-3 bg-orange-50 p-3 rounded-lg border-l-4 border-orange-500">
                    <i class="fas fa-arrow-up text-orange-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-orange-800">Gross margin rendah</p>
                        <p class="text-orange-700 text-xs mt-1">Pertimbangkan untuk menaikkan harga jual atau mencari supplier dengan harga lebih baik.</p>
                    </div>
                </div>
            @endif
            
            @if($operatingRatio > 85)
                <div class="flex items-start gap-3 bg-orange-50 p-3 rounded-lg border-l-4 border-orange-500">
                    <i class="fas fa-cut text-orange-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-orange-800">Beban operasional tinggi</p>
                        <p class="text-orange-700 text-xs mt-1">Evaluasi pengeluaran dan fokus pada efisiensi operasional.</p>
                    </div>
                </div>
            @endif

            @if($labaBersih >= 0 && $profitMargin >= 15)
                <div class="flex items-start gap-3 bg-green-50 p-3 rounded-lg border-l-4 border-green-500">
                    <i class="fas fa-check-circle text-green-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-green-800">Kinerja keuangan sehat!</p>
                        <p class="text-green-700 text-xs mt-1">Pertahankan strategi saat ini dan fokus pada pertumbuhan.</p>
                    </div>
                </div>
            @endif

            @if($labaBersih >= 0 && $labaBersih < ($penjualan * 0.1))
                <div class="flex items-start gap-3 bg-yellow-50 p-3 rounded-lg border-l-4 border-yellow-500">
                    <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-yellow-800">Laba masih bisa ditingkatkan</p>
                        <p class="text-yellow-700 text-xs mt-1">Fokus pada peningkatan margin dengan optimasi harga atau efisiensi biaya.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('periodSelect');
    const customDateRange = document.getElementById('customDateRange');
    const customDateRange2 = document.getElementById('customDateRange2');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const form = document.getElementById('filterForm');

    // Auto-submit when period changes (except custom)
    periodSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
            customDateRange2.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
            customDateRange2.classList.add('hidden');
            form.submit();
        }
    });

    // Auto-submit for custom dates with debounce
    let timeout;
    function handleDateChange() {
        clearTimeout(timeout);
        if (startDate.value && endDate.value) {
            timeout = setTimeout(function() {
                form.submit();
            }, 500);
        }
    }

    startDate.addEventListener('change', handleDateChange);
    endDate.addEventListener('change', handleDateChange);
});
</script>

@endsection