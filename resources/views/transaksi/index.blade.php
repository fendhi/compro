@extends('layouts.app')

@section('title', 'Point of Sale')
@section('header', 'Point of Sale (POS)')

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .cart-item {
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Payment Method Selection */
    .payment-option input[type="radio"]:checked + div {
        border-color: #3b82f6;
    }
    .payment-option:has(input[type="radio"]:checked) {
        background-color: #eff6ff;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Product Grid - Left Side (2/3) -->
    <div class="lg:col-span-2 space-y-4">
        
        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchProduct" placeholder="Cari produk (nama atau kode)..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <select id="filterKategori" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    @if(isset($kategoris) && count($kategoris) > 0)
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div id="productsGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[600px] overflow-y-auto">
                @forelse($barangs as $barang)
                <div class="product-card bg-white rounded-xl p-3 border-2 border-gray-200 hover:border-blue-400 shadow-sm" 
                     data-id="{{ $barang->id }}"
                     data-nama="{{ $barang->nama }}"
                     data-harga="{{ $barang->harga }}"
                     data-stok="{{ $barang->stok }}"
                     data-kategori="{{ $barang->kategori_id }}"
                     data-kode="{{ $barang->kode_barang }}"
                     onclick="addToCart(this)">
                    <!-- Product Image -->
                    <div class="aspect-square bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-lg mb-3 flex items-center justify-center overflow-hidden border border-gray-100 relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-100/30 to-purple-100/30"></div>
                        <div class="relative z-10 text-center">
                            <i class="fas fa-box-open text-5xl text-blue-400 mb-2"></i>
                            <div class="text-xs font-semibold text-blue-500 uppercase tracking-wide">{{ $barang->kategori->nama ?? 'Produk' }}</div>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="space-y-2">
                        <div>
                            <h4 class="font-bold text-sm text-gray-800 mb-0.5 line-clamp-2 leading-tight">{{ $barang->nama }}</h4>
                            <p class="text-xs text-gray-400 font-mono">{{ $barang->kode_barang }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div>
                                <div class="text-xs text-gray-500 mb-0.5">Harga</div>
                                <span class="text-base font-bold text-blue-600">Rp {{ number_format($barang->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 mb-0.5">Stok</div>
                                <span class="text-sm font-bold px-2 py-1 rounded-lg {{ $barang->stok > 10 ? 'bg-green-100 text-green-700' : ($barang->stok > 0 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $barang->stok }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Add to Cart Indicator -->
                        <button class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold py-2 rounded-lg transition-all flex items-center justify-center gap-1">
                            <i class="fas fa-cart-plus"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-gray-400">
                    <i class="fas fa-box-open text-5xl mb-3"></i>
                    <p class="text-lg">Belum ada produk</p>
                    <a href="{{ route('barang.index') }}" class="inline-block mt-3 text-blue-500 hover:text-blue-600">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Produk
                    </a>
                </div>
                @endforelse
            </div>
            <div id="noProducts" class="hidden text-center py-12 text-gray-400">
                <i class="fas fa-search text-4xl mb-3"></i>
                <p>Produk tidak ditemukan</p>
            </div>
        </div>
    </div>

    <!-- Shopping Cart - Right Side (1/3) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
            
            <!-- Cart Header -->
            <div class="flex items-center justify-between mb-4 pb-4 border-b">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-blue-500"></i>
                    Keranjang
                </h3>
                <button onclick="clearCart()" class="text-sm text-red-500 hover:text-red-600 transition-colors">
                    <i class="fas fa-trash"></i> Kosongkan
                </button>
            </div>

            <!-- Cart Items -->
            <div id="cartItems" class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-shopping-basket text-4xl mb-2"></i>
                    <p class="text-sm">Keranjang masih kosong</p>
                </div>
            </div>

            <!-- Transaction Discount -->
            <div class="mb-4 pb-4 border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-percentage text-blue-500 mr-1"></i>
                    Diskon Transaksi (%)
                </label>
                <div class="relative">
                    <input type="number" id="diskonValue" value="0" min="0" max="100" step="1"
                           class="w-full pl-4 pr-12 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                           placeholder="0"
                           oninput="updateTotal()">
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold text-lg">
                        %
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i> Masukkan persentase diskon (0-100%)
                </p>
            </div>

            <!-- Summary -->
            <div class="space-y-2 mb-4 pb-4 border-t pt-4">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal:</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm text-red-600">
                    <span>Diskon:</span>
                    <span id="discountDisplay">Rp 0</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t">
                    <span>TOTAL:</span>
                    <span id="total">Rp 0</span>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-wallet text-blue-500 mr-1"></i>
                        Metode Pembayaran
                    </label>
                    <div class="space-y-2">
                        <!-- Tunai -->
                        <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all payment-option">
                            <input type="radio" name="paymentMethod" value="cash" checked class="w-4 h-4 text-blue-600" onchange="selectPaymentMethod('cash')">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">💵</span>
                                        <div>
                                            <p class="font-semibold text-gray-800">Tunai</p>
                                            <p class="text-xs text-gray-500">Pembayaran Cash</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">Aktif</span>
                                </div>
                            </div>
                        </label>
                        
                        <!-- QRIS -->
                        <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all payment-option">
                            <input type="radio" name="paymentMethod" value="qris" class="w-4 h-4 text-blue-600" onchange="selectPaymentMethod('qris')">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">📱</span>
                                        <div>
                                            <p class="font-semibold text-gray-800">QRIS</p>
                                            <p class="text-xs text-gray-500">Scan & Pay</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">Aktif</span>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Transfer BCA -->
                        <label class="flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all payment-option">
                            <input type="radio" name="paymentMethod" value="transfer_bca" class="w-4 h-4 text-blue-600" onchange="selectPaymentMethod('transfer_bca')">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">🏦</span>
                                        <div>
                                            <p class="font-semibold text-gray-800">Transfer BCA</p>
                                            <p class="text-xs text-gray-500">Bank Central Asia</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">Aktif</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- QRIS Display -->
                <div id="qrisDisplay" class="hidden bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-4 border-2 border-blue-200">
                    <div class="text-center">
                        <div class="flex items-center justify-center gap-2 mb-3">
                            <i class="fas fa-qrcode text-blue-600 text-xl"></i>
                            <h3 class="font-bold text-gray-800">Scan QR Code</h3>
                        </div>
                        <div class="bg-white p-4 rounded-xl inline-block shadow-lg mb-3">
                            <img id="qrisImage" src="" alt="QRIS Code" class="w-48 h-48 mx-auto">
                        </div>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-mobile-alt mr-1"></i>
                            Scan dengan aplikasi e-wallet Anda
                        </p>
                        <div class="flex items-center justify-center gap-2 text-xs text-gray-500">
                            <span>GoPay</span> • 
                            <span>OVO</span> • 
                            <span>Dana</span> • 
                            <span>ShopeePay</span>
                        </div>
                    </div>
                </div>
                
                <!-- Transfer BCA Display -->
                <div id="transferDisplay" class="hidden bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border-2 border-blue-300">
                    <div class="text-center">
                        <div class="flex items-center justify-center gap-2 mb-3">
                            <i class="fas fa-university text-blue-600 text-xl"></i>
                            <h3 class="font-bold text-gray-800">Informasi Transfer</h3>
                        </div>
                        <div class="bg-white rounded-xl p-4 mb-3 shadow-lg">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-sm text-gray-600">Bank</span>
                                    <span class="font-bold text-gray-800">BCA (Bank Central Asia)</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-sm text-gray-600">No. Rekening</span>
                                    <div class="text-right">
                                        <span id="accountNumber" class="font-bold text-xl text-blue-600">1234567890</span>
                                        <button onclick="copyAccountNumber()" class="ml-2 text-blue-500 hover:text-blue-600">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-gray-600">Atas Nama</span>
                                    <span class="font-bold text-gray-800">ORIND STORE</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-xs text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Transfer sesuai jumlah total: <span id="transferAmount" class="font-bold">Rp 0</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Amount (hanya untuk Tunai) -->
                <div id="paymentAmountSection">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bayar (Rp)</label>
                    <input type="number" id="paymentAmount" value="0" min="0" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                           oninput="calculateChange()">
                </div>
                
                <div id="changeSection" class="hidden bg-green-50 rounded-lg p-3 border border-green-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Kembalian:</span>
                        <span id="change" class="text-xl font-bold text-green-600">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2">
                <!-- Button untuk Tunai -->
                <button id="btnProcessCash" onclick="processPayment()" 
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-check-circle mr-2"></i>
                    Proses Pembayaran
                </button>
                
                <!-- Button untuk QRIS -->
                <button id="btnConfirmQRIS" onclick="confirmDigitalPayment('qris')" 
                        class="hidden w-full bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-check-double mr-2"></i>
                    Konfirmasi Pembayaran QRIS Diterima
                </button>
                
                <!-- Button untuk Transfer -->
                <button id="btnConfirmTransfer" onclick="confirmDigitalPayment('transfer_bca')" 
                        class="hidden w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-check-double mr-2"></i>
                    Konfirmasi Transfer BCA Diterima
                </button>
                
                <button onclick="clearCart()" 
                        class="w-full bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition-all">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Recent Transactions -->
<div class="mt-6 bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-history text-purple-500"></i>
        Transaksi Hari Ini
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kasir</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transaksis as $index => $transaksi)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ $transaksi->tanggal->format('H:i') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $transaksi->user->name }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-blue-600">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $transaksi->details->count() }} item</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('transaksi.show', $transaksi->id) }}" 
                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>Belum ada transaksi hari ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 for better notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- QRIS Auto-Verification Script - DISABLED FOR LOCALHOST -->
<!-- <script src="{{ asset('js/qris-auto-verification.js') }}"></script> -->
<!-- POS Cart Script -->
<script src="{{ asset('js/pos-cart-v2.js') }}"></script>
@endpush
