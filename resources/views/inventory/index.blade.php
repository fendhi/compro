@extends('layouts.app')

@section('title', 'Inventory Management')
@section('header', 'Manajemen Inventory')

@push('styles')
<style>
    .modal { transition: opacity 0.3s ease; }
    .modal-content { animation: slideDown 0.3s ease; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }
    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-warehouse text-blue-500"></i>
                Manajemen Inventory
                @if(!$canAdjust)
                    <span class="badge bg-gray-200 text-gray-700 ml-2">View Only</span>
                @endif
            </h2>
            <p class="text-gray-600 mt-1">Kelola stok barang masuk dan keluar</p>
        </div>
        @if($canAdjust)
        <div class="flex gap-2">
            <button onclick="openHistoryPage()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-all flex items-center gap-2">
                <i class="fas fa-history"></i>
                Riwayat
            </button>
            <button onclick="openStockInModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-down"></i>
                Stok Masuk
            </button>
            <button onclick="openStockOutModal()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-up"></i>
                Stok Keluar
            </button>
            <button onclick="openStockOpnameModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-clipboard-check"></i>
                Opname
            </button>
        </div>
        @else
        <div class="text-gray-500 text-sm italic flex items-center gap-2">
            <i class="fas fa-eye"></i>
            Mode tampilan saja
        </div>
        @endif
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockItems->count() > 0)
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
    <div class="flex items-start">
        <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
        <div>
            <h3 class="text-red-800 font-semibold">Peringatan Stok Rendah!</h3>
            <p class="text-red-700 mt-1">Terdapat {{ $lowStockItems->count() }} produk dengan stok di bawah minimum.</p>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($lowStockItems->take(5) as $item)
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                    {{ $item->nama }} ({{ $item->stok }}/{{ $item->stok_minimum }})
                </span>
                @endforeach
                @if($lowStockItems->count() > 5)
                <span class="text-red-700 text-sm">dan {{ $lowStockItems->count() - 5 }} lainnya...</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Search & Filter -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <div class="flex flex-col md:flex-row gap-3">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchBarang" placeholder="Cari barang..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   onkeyup="filterTable()">
        </div>
        <select id="filterStok" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="filterTable()">
            <option value="">Semua Stok</option>
            <option value="low">Stok Rendah</option>
            <option value="normal">Stok Normal</option>
        </select>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" id="inventoryTable">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok Saat Ini</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok Minimum</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="tableBody">
                @forelse($barangs as $index => $barang)
                <tr class="hover:bg-gray-50 transition-colors" 
                    data-nama="{{ strtolower($barang->nama) }}"
                    data-kode="{{ strtolower($barang->kode_barang) }}"
                    data-stok="{{ $barang->stok }}"
                    data-minimum="{{ $barang->stok_minimum }}">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-mono text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $barang->kode_barang }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-blue-500"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $barang->nama }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $barang->kategori->nama ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-lg font-bold {{ $barang->stok <= $barang->stok_minimum ? 'text-red-600' : 'text-green-600' }}">
                            {{ $barang->stok }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $barang->stok_minimum }}</td>
                    <td class="px-6 py-4">
                        @if($barang->stok <= $barang->stok_minimum)
                            <span class="badge badge-danger">
                                <i class="fas fa-exclamation-circle"></i> Stok Rendah
                            </span>
                        @elseif($barang->stok <= $barang->stok_minimum * 2)
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i> Perlu Restock
                            </span>
                        @else
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Aman
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openStockInModal({{ $barang->id }})" 
                                    class="text-green-600 hover:text-green-800 p-2" 
                                    title="Stok Masuk">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button onclick="openStockOutModal({{ $barang->id }})" 
                                    class="text-red-600 hover:text-red-800 p-2" 
                                    title="Stok Keluar">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button onclick="openStockOpnameModal({{ $barang->id }})" 
                                    class="text-blue-600 hover:text-blue-800 p-2" 
                                    title="Stock Opname">
                                <i class="fas fa-clipboard-check"></i>
                            </button>
                            <button onclick="openAdjustmentModal({{ $barang->id }})" 
                                    class="text-yellow-600 hover:text-yellow-800 p-2" 
                                    title="Penyesuaian">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data barang</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Stock In -->
<div id="stockInModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 rounded-t-xl">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-arrow-down"></i>
                Stok Masuk
            </h3>
        </div>
        <form id="stockInForm" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Barang *</label>
                <select name="barang_id" id="stockInBarangId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}">{{ $barang->nama }} (Stok: {{ $barang->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah *</label>
                <input type="number" name="quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" min="1" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Contoh: Pembelian dari supplier"></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Referensi</label>
                <input type="text" name="referensi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Contoh: PO-001">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeStockInModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Stock Out -->
<div id="stockOutModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-xl">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-arrow-up"></i>
                Stok Keluar
            </h3>
        </div>
        <form id="stockOutForm" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Barang *</label>
                <select name="barang_id" id="stockOutBarangId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}">{{ $barang->nama }} (Stok: {{ $barang->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah *</label>
                <input type="number" name="quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" min="1" required>
                <p class="text-xs text-gray-500 mt-1">Stok tersedia: <span id="stokTersedia">0</span></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Contoh: Barang rusak, barang hilang, dll"></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Referensi</label>
                <input type="text" name="referensi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Contoh: ADJ-001">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeStockOutModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Stock Opname -->
<div id="stockOpnameModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 rounded-t-xl">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-clipboard-check"></i>
                Stock Opname
            </h3>
        </div>
        <form id="stockOpnameForm" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Barang *</label>
                <select name="barang_id" id="stockOpnameBarangId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}">{{ $barang->nama }} (Stok Sistem: {{ $barang->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4 bg-gray-100 p-3 rounded-lg">
                <p class="text-sm text-gray-700">Stok di Sistem: <span id="stokSistem" class="font-bold">0</span></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Stok Fisik (Hasil Hitung) *</label>
                <input type="number" name="stok_fisik" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan *</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Contoh: Stock opname bulanan Januari 2025" required></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeStockOpnameModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Adjustment -->
<div id="adjustmentModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 rounded-t-xl">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Penyesuaian Stok
            </h3>
        </div>
        <form id="adjustmentForm" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Barang *</label>
                <select name="barang_id" id="adjustmentBarangId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}">{{ $barang->nama }} (Stok: {{ $barang->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Penyesuaian *</label>
                <select name="adjustment_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="add">Tambah Stok</option>
                    <option value="subtract">Kurangi Stok</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah *</label>
                <input type="number" name="quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" min="1" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan *</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="Contoh: Penyesuaian karena kesalahan input" required></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeAdjustmentModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/inventory-management.js') }}"></script>
@endpush
