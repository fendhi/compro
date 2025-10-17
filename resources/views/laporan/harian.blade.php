@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('header', 'Laporan Transaksi Harian')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
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
                <i class="fas fa-calendar-day text-blue-500"></i>
                Laporan Harian
            </h2>
            <p class="text-gray-600 mt-1">Ringkasan transaksi per tanggal</p>
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
                <i class="fas fa-calendar text-blue-500"></i> Tanggal
                <span class="text-xs text-gray-500">(auto-filter)</span>
            </label>
            <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        @if(auth()->user()->canViewAllTransactions())
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user text-blue-500"></i> Kasir
                <span class="text-xs text-gray-500">(auto-filter)</span>
            </label>
            <select id="kasir" name="kasir" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Kasir</option>
                @foreach($kasirs as $kasir)
                <option value="{{ $kasir->id }}" {{ request('kasir') == $kasir->id ? 'selected' : '' }}>
                    {{ $kasir->name }}
                </option>
                @endforeach
            </select>
        </div>
        @else
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user text-blue-500"></i> Kasir
            </label>
            <input type="text" value="{{ auth()->user()->name }}" readonly 
                   class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 cursor-not-allowed">
        </div>
        @endif
        
        <div class="flex items-end gap-2">
            <a href="{{ route('laporan.harian') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-all shadow-md hover:shadow-lg font-semibold" title="Reset Filter">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Total Transaksi</p>
                <p class="text-3xl font-bold mt-1">{{ $transaksis->count() }}</p>
                <p class="text-blue-100 text-xs mt-2">
                    <i class="fas fa-receipt"></i> {{ date('d M Y', strtotime(request('tanggal', date('Y-m-d')))) }}
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
                    <i class="fas fa-arrow-up"></i> Omzet hari ini
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Total Item Terjual</p>
                <p class="text-3xl font-bold mt-1">{{ $transaksis->sum(function($t) { return $t->details->sum('jumlah'); }) }}</p>
                <p class="text-purple-100 text-xs mt-2">
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
                <p class="text-orange-100 text-sm">Rata-rata/Transaksi</p>
                <p class="text-3xl font-bold mt-1">Rp {{ $transaksis->count() > 0 ? number_format($transaksis->avg('total'), 0, ',', '.') : 0 }}</p>
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

<!-- Payment Methods Breakdown -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-credit-card text-blue-500"></i>
        Metode Pembayaran
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
        @endphp
        <div class="border-2 border-{{ $payment['color'] }}-200 bg-{{ $payment['color'] }}-50 rounded-lg p-4 text-center">
            <i class="fas fa-{{ $payment['icon'] }} text-{{ $payment['color'] }}-500 text-2xl mb-2"></i>
            <p class="text-sm text-gray-600">{{ $payment['label'] }}</p>
            <p class="text-xl font-bold text-gray-800">{{ $count }}</p>
            <p class="text-xs text-gray-500">Rp {{ number_format($total / 1000, 0) }}K</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Top Products -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-trophy text-yellow-500"></i>
        Top 5 Produk Terlaris
    </h3>
    <div class="space-y-3">
        @php
            $topProducts = \App\Models\TransaksiDetail::whereHas('transaksi', function($q) {
                $q->whereDate('created_at', request('tanggal', date('Y-m-d')));
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
            ->take(5);
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
        <p class="text-gray-500 text-center py-4">Belum ada transaksi</p>
        @endforelse
    </div>
</div>

<!-- Transaction Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6 border-b">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-list text-blue-500"></i>
            Detail Transaksi
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kasir</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Metode</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transaksis as $transaksi)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm font-semibold text-blue-600">{{ $transaksi->no_invoice }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $transaksi->created_at->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-gray-800">{{ $transaksi->user->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                            {{ $transaksi->details->sum('jumlah') }} item
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $metodeBadge = [
                                'cash' => 'bg-green-100 text-green-700',
                                'debit' => 'bg-blue-100 text-blue-700',
                                'credit' => 'bg-purple-100 text-purple-700',
                                'ewallet' => 'bg-orange-100 text-orange-700',
                                'qris' => 'bg-red-100 text-red-700'
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $metodeBadge[$transaksi->metode_pembayaran] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ strtoupper($transaksi->metode_pembayaran) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-bold text-gray-800">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada transaksi pada tanggal ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td colspan="5" class="px-6 py-4 text-right text-gray-800">TOTAL:</td>
                    <td class="px-6 py-4 text-right text-lg text-blue-600">
                        Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Print Area -->
<div class="print-area mt-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
        <h2 class="text-lg">Laporan Transaksi Harian</h2>
        <p>Tanggal: {{ date('d F Y', strtotime(request('tanggal', date('Y-m-d')))) }}</p>
    </div>
    
    <table class="w-full border-collapse border border-gray-300 text-sm">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-2 py-1">No Invoice</th>
                <th class="border border-gray-300 px-2 py-1">Waktu</th>
                <th class="border border-gray-300 px-2 py-1">Kasir</th>
                <th class="border border-gray-300 px-2 py-1">Item</th>
                <th class="border border-gray-300 px-2 py-1">Metode</th>
                <th class="border border-gray-300 px-2 py-1">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $transaksi)
            <tr>
                <td class="border border-gray-300 px-2 py-1">{{ $transaksi->no_invoice }}</td>
                <td class="border border-gray-300 px-2 py-1">{{ $transaksi->created_at->format('H:i') }}</td>
                <td class="border border-gray-300 px-2 py-1">{{ $transaksi->user->name }}</td>
                <td class="border border-gray-300 px-2 py-1 text-center">{{ $transaksi->details->sum('jumlah') }}</td>
                <td class="border border-gray-300 px-2 py-1 text-center">{{ strtoupper($transaksi->metode_pembayaran) }}</td>
                <td class="border border-gray-300 px-2 py-1 text-right">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="font-bold bg-gray-100">
                <td colspan="5" class="border border-gray-300 px-2 py-1 text-right">TOTAL:</td>
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
        const tanggalInput = document.getElementById('tanggal');
        const kasirSelect = document.getElementById('kasir');
        
        // Auto-submit when date changes
        tanggalInput.addEventListener('change', function() {
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
<script src="{{ asset('js/laporan-harian.js') }}"></script>
@endpush
