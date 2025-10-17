@extends('layouts.app')

@section('title', 'Laporan Bulanan')
@section('header', 'Laporan Transaksi Bulanan')

@push('styles')
<style>
    .print-area { display: none; }
    @media print {
        .no-print { display: none !important; }
        .print-area { display: block !important; }
        body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    }
</style>
@endpush

@section('content')
<!-- Header with Actions -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6 no-print">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-purple-500"></i>
                Laporan Bulanan
            </h2>
            <p class="text-gray-600 mt-1">Ringkasan transaksi per bulan</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <button onclick="exportExcel()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                Excel
            </button>
            <button onclick="exportPDF()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                PDF
            </button>
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-print"></i>
                Print
            </button>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6 no-print">
    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-calendar text-purple-500"></i> Bulan
                <span class="text-xs text-gray-500">(auto-filter)</span>
            </label>
            <input type="month" id="bulan" name="bulan" value="{{ request('bulan', date('Y-m')) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user text-purple-500"></i> Kasir
                <span class="text-xs text-gray-500">(auto-filter)</span>
            </label>
            <select id="kasir" name="kasir" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="">Semua Kasir</option>
                @foreach($kasirs as $kasir)
                <option value="{{ $kasir->id }}" {{ request('kasir') == $kasir->id ? 'selected' : '' }}>
                    {{ $kasir->name }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="flex items-end gap-2">
            <a href="{{ route('laporan.bulanan') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition" title="Reset Filter">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Total Transaksi</p>
                <p class="text-3xl font-bold mt-1">{{ $transaksis->count() }}</p>
                <p class="text-purple-100 text-xs mt-2">
                    <i class="fas fa-receipt"></i> {{ date('F Y', strtotime(request('bulan', date('Y-m')))) }}
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Total Pendapatan</p>
                <p class="text-3xl font-bold mt-1">Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}</p>
                <p class="text-green-100 text-xs mt-2">
                    <i class="fas fa-arrow-up"></i> Omzet bulanan
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Total Item Terjual</p>
                <p class="text-3xl font-bold mt-1">{{ $transaksis->sum(function($t) { return $t->details->sum('jumlah'); }) }}</p>
                <p class="text-blue-100 text-xs mt-2">
                    <i class="fas fa-box"></i> Barang terjual
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-cubes text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Rata-rata/Hari</p>
                <p class="text-3xl font-bold mt-1">Rp {{ $transaksis->count() > 0 ? number_format($transaksis->sum('total') / date('t', strtotime(request('bulan', date('Y-m')))), 0, ',', '.') : 0 }}</p>
                <p class="text-orange-100 text-xs mt-2">
                    <i class="fas fa-chart-line"></i> Nilai rata-rata
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-calculator text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Daily Summary Chart/Table -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-chart-bar text-purple-500"></i>
        Ringkasan Per Tanggal
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Transaksi</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Item Terjual</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase no-print">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php
                    $bulan = request('bulan', date('Y-m'));
                    $daysInMonth = date('t', strtotime($bulan));
                    $dailySummary = [];
                    
                    for ($i = 1; $i <= $daysInMonth; $i++) {
                        $date = date('Y-m-d', strtotime("$bulan-$i"));
                        $dailyTransaksi = $transaksis->filter(function($t) use ($date) {
                            return $t->created_at->format('Y-m-d') == $date;
                        });
                        
                        $dailySummary[] = [
                            'date' => $date,
                            'count' => $dailyTransaksi->count(),
                            'items' => $dailyTransaksi->sum(function($t) { return $t->details->sum('jumlah'); }),
                            'total' => $dailyTransaksi->sum('total')
                        ];
                    }
                @endphp
                
                @foreach($dailySummary as $day)
                <tr class="hover:bg-gray-50 transition-colors {{ $day['count'] == 0 ? 'opacity-50' : '' }}">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-800">{{ date('d F Y', strtotime($day['date'])) }}</span>
                        <span class="text-xs text-gray-500 ml-2">{{ date('l', strtotime($day['date'])) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $day['count'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-gray-800 font-semibold">{{ $day['items'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-bold text-gray-800">Rp {{ number_format($day['total'], 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-4 text-center no-print">
                        @if($day['count'] > 0)
                        <a href="{{ route('laporan.harian', ['tanggal' => $day['date']]) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        @else
                        <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td class="px-6 py-4 text-right">TOTAL:</td>
                    <td class="px-6 py-4 text-center text-purple-600">{{ $transaksis->count() }}</td>
                    <td class="px-6 py-4 text-center text-blue-600">{{ $transaksis->sum(function($t) { return $t->details->sum('jumlah'); }) }}</td>
                    <td class="px-6 py-4 text-right text-lg text-green-600">
                        Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 no-print"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Top Products Monthly -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-trophy text-yellow-500"></i>
        Top 10 Produk Terlaris Bulan Ini
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @php
            $topProducts = \App\Models\TransaksiDetail::whereHas('transaksi', function($q) use ($bulan) {
                $q->whereYear('created_at', date('Y', strtotime($bulan)))
                  ->whereMonth('created_at', date('m', strtotime($bulan)));
                if (request('kasir')) {
                    $q->where('user_id', request('kasir'));
                }
            })->with('barang')
            ->get()
            ->groupBy('barang_id')
            ->map(function($group) {
                return [
                    'barang' => $group->first()->barang,
                    'qty' => $group->sum('jumlah'),
                    'total' => $group->sum('subtotal')
                ];
            })
            ->sortByDesc('qty')
            ->take(10);
        @endphp
        
        @forelse($topProducts as $index => $product)
        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center justify-center w-10 h-10 rounded-full 
                        {{ $index == 0 ? 'bg-yellow-400 text-white' : ($index == 1 ? 'bg-gray-300 text-gray-700' : ($index == 2 ? 'bg-orange-400 text-white' : 'bg-gray-200 text-gray-600')) }}">
                <span class="font-bold">#{{ $index + 1 }}</span>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-800">{{ $product['barang']->nama }}</p>
                <p class="text-sm text-gray-600">{{ $product['barang']->kategori->nama }}</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-gray-800">{{ $product['qty'] }} terjual</p>
                <p class="text-sm text-gray-600">Rp {{ number_format($product['total'], 0, ',', '.') }}</p>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-4 col-span-2">Belum ada transaksi</p>
        @endforelse
    </div>
</div>

<!-- Payment Methods Monthly -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-credit-card text-purple-500"></i>
        Metode Pembayaran Bulan Ini
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @php
            $payments = [
                'cash' => ['label' => 'Tunai', 'icon' => 'money-bill-wave', 'color' => 'green'],
                'qris' => ['label' => 'QRIS', 'icon' => 'qrcode', 'color' => 'red'],
                'transfer_bca' => ['label' => 'Transfer', 'icon' => 'credit-card', 'color' => 'blue']
            ];
        @endphp
        
        @foreach($payments as $key => $payment)
        @php
            $count = $transaksis->where('metode_pembayaran', $key)->count();
            $total = $transaksis->where('metode_pembayaran', $key)->sum('total');
            $percentage = $transaksis->count() > 0 ? round(($count / $transaksis->count()) * 100) : 0;
        @endphp
        <div class="border-2 border-{{ $payment['color'] }}-200 bg-{{ $payment['color'] }}-50 rounded-lg p-4 text-center">
            <i class="fas fa-{{ $payment['icon'] }} text-{{ $payment['color'] }}-500 text-2xl mb-2"></i>
            <p class="text-sm text-gray-600">{{ $payment['label'] }}</p>
            <p class="text-xl font-bold text-gray-800">{{ $count }}</p>
            <p class="text-xs text-gray-500">{{ $percentage }}%</p>
            <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($total / 1000, 0) }}K</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Print Area -->
<div class="print-area mt-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
        <h2 class="text-lg">Laporan Transaksi Bulanan</h2>
        <p>Periode: {{ date('F Y', strtotime(request('bulan', date('Y-m')))) }}</p>
    </div>
    
    <div class="mb-4">
        <p><strong>Total Transaksi:</strong> {{ $transaksis->count() }}</p>
        <p><strong>Total Pendapatan:</strong> Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}</p>
        <p><strong>Total Item Terjual:</strong> {{ $transaksis->sum(function($t) { return $t->details->sum('jumlah'); }) }}</p>
    </div>
    
    <table class="w-full border-collapse border border-gray-300 text-sm">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-2 py-1">Tanggal</th>
                <th class="border border-gray-300 px-2 py-1">Transaksi</th>
                <th class="border border-gray-300 px-2 py-1">Item</th>
                <th class="border border-gray-300 px-2 py-1">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySummary as $day)
            <tr>
                <td class="border border-gray-300 px-2 py-1">{{ date('d/m/Y', strtotime($day['date'])) }}</td>
                <td class="border border-gray-300 px-2 py-1 text-center">{{ $day['count'] }}</td>
                <td class="border border-gray-300 px-2 py-1 text-center">{{ $day['items'] }}</td>
                <td class="border border-gray-300 px-2 py-1 text-right">Rp {{ number_format($day['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="font-bold bg-gray-100">
                <td class="border border-gray-300 px-2 py-1 text-right">TOTAL:</td>
                <td class="border border-gray-300 px-2 py-1 text-center">{{ $transaksis->count() }}</td>
                <td class="border border-gray-300 px-2 py-1 text-center">{{ $transaksis->sum(function($t) { return $t->details->sum('jumlah'); }) }}</td>
                <td class="border border-gray-300 px-2 py-1 text-right">Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="mt-4 text-sm">
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>
</div>

<script>
    // Auto-submit filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filterForm');
        const bulanInput = document.getElementById('bulan');
        const kasirSelect = document.getElementById('kasir');
        
        // Auto-submit when month changes
        bulanInput.addEventListener('change', function() {
            form.submit();
        });
        
        // Auto-submit when kasir changes
        kasirSelect.addEventListener('change', function() {
            form.submit();
        });
    });
</script>

@endsection

@push('scripts')
<script src="{{ asset('js/laporan-bulanan.js') }}"></script>
@endpush
