@extends('layouts.app')

@section('title', 'Detail Transaksi - OrindPOS')
@section('header', 'Detail Transaksi')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('transaksi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke POS
        </a>
    </div>

    <!-- Receipt -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 text-center">
            <h2 class="text-2xl font-bold mb-2">OrindPOS</h2>
            <p class="text-blue-100 text-sm">Sistem Point of Sale Modern</p>
        </div>

        <!-- Receipt Content -->
        <div id="receiptContent" class="p-8">
            
            <!-- Transaction Info -->
            <div class="border-b pb-4 mb-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">No. Transaksi</p>
                        <p class="font-bold text-gray-800">#{{ $transaksi->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Tanggal</p>
                        <p class="font-bold text-gray-800">{{ $transaksi->tanggal->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Kasir</p>
                        <p class="font-bold text-gray-800">{{ $transaksi->user->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Metode Pembayaran</p>
                        <p class="font-bold text-gray-800">
                            @switch($transaksi->payment_method)
                                @case('cash')
                                    💵 Tunai
                                    @break
                                @case('qris')
                                    � QRIS
                                    @break
                                @case('transfer_bca')
                                    🏦 Transfer BCA
                                    @break
                                @case('debit')
                                    💳 Kartu Debit
                                    @break
                                @case('credit')
                                    � Kartu Kredit
                                    @break
                                @case('transfer')
                                    🏦 Transfer Bank
                                    @break
                                @default
                                    {{ strtoupper(str_replace('_', ' ', $transaksi->payment_method)) }}
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="mb-6">
                <table class="w-full">
                    <thead class="border-b">
                        <tr class="text-left text-sm text-gray-600">
                            <th class="pb-3">Produk</th>
                            <th class="pb-3 text-right">Harga</th>
                            <th class="pb-3 text-center">Qty</th>
                            <th class="pb-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($transaksi->details as $detail)
                        <tr class="text-sm">
                            <td class="py-3">
                                <p class="font-semibold text-gray-800">{{ $detail->barang->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $detail->barang->kode_barang }}</p>
                                @if($detail->diskon_persen > 0)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-red-100 text-red-600 text-xs rounded">
                                        <i class="fas fa-tag"></i> Diskon {{ $detail->diskon_persen }}%
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                @if($detail->diskon_persen > 0)
                                    <div class="text-gray-400 line-through text-xs">
                                        Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                    </div>
                                    <div class="text-gray-800 font-semibold">
                                        Rp {{ number_format($detail->harga_setelah_diskon ?? ($detail->harga - ($detail->harga * $detail->diskon_persen / 100)), 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-gray-600">
                                        Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 text-center text-gray-600">
                                {{ $detail->jumlah }}
                            </td>
                            <td class="py-3 text-right">
                                <div class="font-semibold text-gray-800">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </div>
                                @if($detail->diskon_amount > 0)
                                    <div class="text-xs text-red-600">
                                        Hemat: Rp {{ number_format($detail->diskon_amount, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold">Rp {{ number_format($transaksi->details->sum('subtotal'), 0, ',', '.') }}</span>
                </div>
                
                @if($transaksi->diskon_amount > 0)
                <div class="flex justify-between text-red-600">
                    <span>
                        Diskon Transaksi
                        @if($transaksi->diskon_type === 'percentage')
                            ({{ $transaksi->diskon_value }}%)
                        @elseif($transaksi->diskon_type === 'nominal')
                            (Nominal)
                        @endif:
                    </span>
                    <span class="font-semibold">- Rp {{ number_format($transaksi->diskon_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between text-xl font-bold text-gray-800 pt-2 border-t">
                    <span>Total:</span>
                    <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Bayar:</span>
                    <span class="font-semibold">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-lg font-semibold text-green-600">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t text-center text-sm text-gray-500">
                <p class="mb-2">Terima kasih atas kunjungan Anda</p>
                <p class="text-xs">OrindPOS - Sistem Kasir Modern</p>
                <p class="text-xs mt-1">{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>

        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-8 py-4 flex flex-wrap gap-3 justify-end border-t">
            <a href="{{ route('transaksi.print', $transaksi->id) }}" target="_blank" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-print"></i>
                Cetak Struk Thermal
            </a>
            <button onclick="window.print()" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                Print/Save PDF
            </button>
            <a href="{{ route('transaksi.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-times"></i>
                Tutup
            </a>
        </div>

    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #receiptContent, #receiptContent * {
            visibility: visible;
        }
        #receiptContent {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Print functionality handled by browser
    // Thermal print opens in new window
</script>
@endpush

@endsection
