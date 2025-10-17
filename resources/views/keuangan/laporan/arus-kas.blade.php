@extends('layouts.app')

@section('title', 'Laporan Arus Kas')
@section('header', 'Keuangan - Laporan Arus Kas')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Arus Kas</h1>
        <p class="text-gray-600 text-sm mt-1">Cash Flow Statement - Analisis arus kas masuk & keluar</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('keuangan.arus-kas') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
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
                <a href="{{ route('keuangan.arus-kas') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition" title="Reset Filter">
                    <i class="fas fa-redo"></i>
                </a>
                <a href="{{ route('keuangan.arus-kas.pdf', request()->all()) }}" 
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
        
        <!-- Laporan Arus Kas Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden" id="printable-area">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-6 text-white">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold mb-1">LAPORAN ARUS KAS</h2>
                        <p class="text-sm opacity-90">{{ config('app.name', 'OrindPOS Vapor') }}</p>
                        <p class="text-xs opacity-75 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    
                    <!-- SALDO AWAL -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
                            <span class="font-bold text-gray-800 text-lg">SALDO AWAL</span>
                            <span class="font-bold text-gray-600 text-xl">{{ 'Rp ' . number_format($saldoAwal, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-right">Saldo sebelum periode ini</p>
                    </div>

                    <hr class="my-6 border-gray-300">

                    <!-- KAS MASUK -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4">KAS MASUK (CASH IN)</h3>
                        <div class="space-y-2">
                            @forelse($cashInByMethod as $method => $total)
                            <div class="flex justify-between items-center pl-4 hover:bg-gray-50 py-2 rounded">
                                <div class="flex items-center gap-2">
                                    @if($method == 'cash')
                                        <i class="fas fa-money-bill-wave text-green-500"></i>
                                        <span class="text-gray-700">Tunai</span>
                                    @elseif($method == 'transfer_bca')
                                        <i class="fas fa-university text-blue-500"></i>
                                        <span class="text-gray-700">Transfer BCA</span>
                                    @elseif($method == 'qris')
                                        <i class="fas fa-qrcode text-red-500"></i>
                                        <span class="text-gray-700">QRIS</span>
                                    @else
                                        <i class="fas fa-question-circle text-gray-500"></i>
                                        <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                                    @endif
                                </div>
                                <span class="font-semibold text-green-600">{{ 'Rp ' . number_format($total, 0, ',', '.') }}</span>
                            </div>
                            @empty
                            <div class="text-center text-gray-400 py-4">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="text-sm">Belum ada kas masuk</p>
                            </div>
                            @endforelse
                            
                            @if(count($cashInByMethod) > 0)
                            <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg border-l-4 border-green-500 mt-3">
                                <span class="font-bold text-gray-800">Total Kas Masuk</span>
                                <span class="font-bold text-green-600 text-lg">{{ 'Rp ' . number_format($totalCashIn, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-6 border-gray-300">

                    <!-- KAS KELUAR -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4">KAS KELUAR (CASH OUT)</h3>
                        <div class="space-y-2">
                            @forelse($cashOutByCategory as $category)
                            <div class="flex justify-between items-center pl-4 hover:bg-gray-50 py-2 rounded">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->warna }}"></span>
                                    <span class="text-gray-700">{{ $category->nama }}</span>
                                </div>
                                <span class="font-semibold text-red-600">{{ 'Rp ' . number_format($category->total_expense, 0, ',', '.') }}</span>
                            </div>
                            @empty
                            <div class="text-center text-gray-400 py-4">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="text-sm">Belum ada kas keluar</p>
                            </div>
                            @endforelse
                            
                            @if(count($cashOutByCategory) > 0)
                            <div class="flex justify-between items-center bg-red-50 p-3 rounded-lg border-l-4 border-red-500 mt-3">
                                <span class="font-bold text-gray-800">Total Kas Keluar</span>
                                <span class="font-bold text-red-600 text-lg">{{ 'Rp ' . number_format($totalCashOut, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-6 border-gray-300 border-2">

                    <!-- KAS BERSIH -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center bg-{{ $kasBersih >= 0 ? 'blue' : 'red' }}-50 p-4 rounded-lg border-l-4 border-{{ $kasBersih >= 0 ? 'blue' : 'red' }}-500">
                            <span class="font-bold text-gray-800 text-lg">KAS BERSIH</span>
                            <span class="font-bold text-{{ $kasBersih >= 0 ? 'blue' : 'red' }}-600 text-xl">{{ 'Rp ' . number_format($kasBersih, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-right">Kas Masuk - Kas Keluar</p>
                    </div>

                    <hr class="my-6 border-gray-300 border-2">

                    <!-- SALDO AKHIR -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center bg-gradient-to-r from-{{ $saldoAkhir >= 0 ? 'green' : 'red' }}-500 to-{{ $saldoAkhir >= 0 ? 'green' : 'red' }}-600 p-6 rounded-lg shadow-lg text-white">
                            <div>
                                <span class="text-sm opacity-90">SALDO AKHIR</span>
                                <h2 class="font-bold text-3xl mt-1">{{ 'Rp ' . number_format(abs($saldoAkhir), 0, ',', '.') }}</h2>
                                <p class="text-xs opacity-75 mt-2">Saldo Awal + Kas Bersih</p>
                            </div>
                            <div class="text-right">
                                <i class="fas fa-wallet text-5xl opacity-20"></i>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Digenerate pada: {{ now()->format('d F Y H:i') }}
                    </div>
                    <div class="flex gap-2">
                        <button onclick="window.print()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                        <a href="{{ route('keuangan.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition text-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Chart Visualization -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar mr-2 text-[#00718F]"></i>
                    Visualisasi Arus Kas
                </h3>
                
                <!-- Bar Chart -->
                <div class="mb-6">
                    <canvas id="cashFlowChart" style="max-height: 300px;"></canvas>
                </div>

                <!-- Summary Cards -->
                <div class="space-y-3">
                    
                    <!-- Cash In Card -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-green-700 font-semibold">Kas Masuk</p>
                                <p class="text-lg font-bold text-green-600">{{ 'Rp ' . number_format($totalCashIn, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Out Card -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-red-700 font-semibold">Kas Keluar</p>
                                <p class="text-lg font-bold text-red-600">{{ 'Rp ' . number_format($totalCashOut, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Net Cash Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-equals"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-blue-700 font-semibold">Kas Bersih</p>
                                <p class="text-lg font-bold text-blue-600">{{ 'Rp ' . number_format($kasBersih, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Flow Ratio -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-purple-700 font-semibold">Cash Flow Ratio</p>
                                <p class="text-lg font-bold text-purple-600">{{ number_format($cashFlowRatio, 2) }}%</p>
                                <p class="text-xs text-purple-600">(Kas Bersih / Kas Masuk)</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-area, #printable-area * {
            visibility: visible;
        }
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Scripts -->
<script>
    // Toggle custom date range
    document.getElementById('periodSelect').addEventListener('change', function() {
        const customRange = document.getElementById('customDateRange');
        const customRange2 = document.getElementById('customDateRange2');
        
        if (this.value === 'custom') {
            customRange.classList.remove('hidden');
            customRange2.classList.remove('hidden');
        } else {
            customRange.classList.add('hidden');
            customRange2.classList.add('hidden');
        }
    });

    // Cash Flow Chart
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Kas Masuk', 'Kas Keluar', 'Kas Bersih'],
            datasets: [{
                label: 'Jumlah (Rp)',
                data: [
                    {{ $totalCashIn }},
                    {{ $totalCashOut }},
                    {{ abs($kasBersih) }}
                ],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    '{{ $kasBersih >= 0 ? "rgba(59, 130, 246, 0.8)" : "rgba(239, 68, 68, 0.8)" }}'
                ],
                borderColor: [
                    'rgb(16, 185, 129)',
                    'rgb(239, 68, 68)',
                    '{{ $kasBersih >= 0 ? "rgb(59, 130, 246)" : "rgb(239, 68, 68)" }}'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
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
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Auto-submit filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filterForm');
        const periodSelect = document.getElementById('periodSelect');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        const customRange = document.getElementById('customDateRange');
        const customRange2 = document.getElementById('customDateRange2');
        
        // Auto-submit when period changes
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customRange.classList.remove('hidden');
                customRange2.classList.remove('hidden');
                // Don't auto-submit for custom, wait for dates
            } else {
                customRange.classList.add('hidden');
                customRange2.classList.add('hidden');
                // Auto-submit for predefined periods
                form.submit();
            }
        });
        
        // Auto-submit when custom dates change (with debounce)
        let dateChangeTimeout;
        function handleDateChange() {
            clearTimeout(dateChangeTimeout);
            
            // Only submit if both dates are filled
            if (periodSelect.value === 'custom' && startDate.value && endDate.value) {
                dateChangeTimeout = setTimeout(() => {
                    form.submit();
                }, 500); // Wait 500ms after last change
            }
        }
        
        if (startDate) startDate.addEventListener('change', handleDateChange);
        if (endDate) endDate.addEventListener('change', handleDateChange);
    });
</script>

@endsection
