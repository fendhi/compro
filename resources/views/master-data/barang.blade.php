@extends('layouts.app')

@section('title', 'Data Barang')
@section('header', 'Master Data Barang')

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
</style>
@endpush

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-box text-blue-500"></i>
                Data Barang
                @if(!$canManage)
                    <span class="badge bg-gray-200 text-gray-700">View Only</span>
                @endif
            </h2>
            <p class="text-gray-600 mt-1">Kelola produk dan inventory Anda</p>
        </div>
        @if($canManage)
        <button onclick="openAddModal()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Tambah Barang
        </button>
        @else
        <div class="text-gray-500 text-sm italic flex items-center gap-2">
            <i class="fas fa-eye"></i>
            Mode tampilan saja
        </div>
        @endif
    </div>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <div class="flex flex-col md:flex-row gap-3">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchBarang" placeholder="Cari barang (nama atau kode)..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   onkeyup="filterTable()">
        </div>
        <select id="filterKategori" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="filterTable()">
            <option value="">Semua Kategori</option>
            @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
            @endforeach
        </select>
        <select id="filterStok" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="filterTable()">
            <option value="">Semua Stok</option>
            <option value="low">Stok Rendah (&lt; 10)</option>
            <option value="medium">Stok Sedang (10-50)</option>
            <option value="high">Stok Tinggi (&gt; 50)</option>
        </select>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Total Produk</p>
                <p class="text-3xl font-bold mt-1">{{ $barangs->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-box text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Total Stok</p>
                <p class="text-3xl font-bold mt-1">{{ $barangs->sum('stok') }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-cubes text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Stok Rendah</p>
                <p class="text-3xl font-bold mt-1">{{ $barangs->where('stok', '<', 10)->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Nilai Inventory</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($barangs->sum(function($b) { return $b->harga * $b->stok; }) / 1000000, 1) }}M</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" id="barangTable">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="tableBody">
                @forelse($barangs as $index => $barang)
                <tr class="hover:bg-gray-50 transition-colors" 
                    data-nama="{{ strtolower($barang->nama) }}"
                    data-kode="{{ strtolower($barang->kode_barang) }}"
                    data-kategori="{{ $barang->kategori_id }}"
                    data-stok="{{ $barang->stok }}">
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
                    <td class="px-6 py-4">
                        <span class="badge bg-purple-100 text-purple-700">
                            <i class="fas fa-tag text-xs"></i> {{ $barang->kategori->nama }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($barang->jenis_barang == 'eceran')
                            <span class="badge bg-blue-100 text-blue-700">
                                <i class="fas fa-shopping-basket text-xs"></i> Eceran
                            </span>
                        @elseif($barang->jenis_barang == 'grosir')
                            <span class="badge bg-green-100 text-green-700">
                                <i class="fas fa-boxes text-xs"></i> Grosir
                            </span>
                        @else
                            <span class="badge bg-orange-100 text-orange-700">
                                <i class="fas fa-handshake text-xs"></i> Konsinyasi
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-blue-600">Rp {{ number_format($barang->harga, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-gray-700 bg-gray-100 px-2 py-1 rounded">
                            {{ strtoupper($barang->satuan) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($barang->stok < 10)
                            <span class="badge bg-red-100 text-red-700">
                                <i class="fas fa-exclamation-circle text-xs"></i> {{ $barang->stok }}
                            </span>
                        @elseif($barang->stok < 50)
                            <span class="badge bg-orange-100 text-orange-700">
                                <i class="fas fa-box text-xs"></i> {{ $barang->stok }}
                            </span>
                        @else
                            <span class="badge bg-green-100 text-green-700">
                                <i class="fas fa-check-circle text-xs"></i> {{ $barang->stok }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if($canManage)
                            <button onclick='openEditModal(@json($barang))' 
                                    class="bg-blue-100 text-blue-600 px-3 py-1 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="inline" onsubmit="return confirmDeleteForm(event, 'Barang {{ $barang->nama }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-600 px-3 py-1 rounded-lg hover:bg-red-200 transition-colors text-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 text-xs italic">View Only</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-box-open text-5xl mb-3"></i>
                        <p class="text-lg">Belum ada data barang</p>
                        <button onclick="openAddModal()" class="mt-3 text-blue-500 hover:text-blue-600">
                            <i class="fas fa-plus-circle"></i> Tambah Barang Pertama
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="noResults" class="hidden px-6 py-12 text-center text-gray-400">
        <i class="fas fa-search text-4xl mb-3"></i>
        <p>Tidak ada barang yang sesuai filter</p>
    </div>
</div>

<!-- Modal -->
<div id="barangModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-box"></i>
                    <span id="modalTitle">Tambah Barang</span>
                </h3>
                <button onclick="closeModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="barangForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" id="methodField" name="_method" value="">
            
            <!-- Error Display -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="text-red-800 font-semibold mb-1">Terjadi Kesalahan!</h4>
                            <ul class="text-red-700 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-box text-blue-500"></i> Nama Barang
                    </label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-4 py-2 border @error('nama') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nama barang" value="{{ old('nama') }}">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-purple-500"></i> Kategori
                    </label>
                    <select name="kategori_id" id="kategori_id" required 
                            class="w-full px-4 py-2 border @error('kategori_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="text-red-500 text-xs mt-1">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-layer-group text-indigo-500"></i> Jenis Barang
                    </label>
                    <input type="text" name="jenis_barang" id="jenis_barang" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Eceran, Grosir, Konsinyasi, dll">
                    <p class="text-xs text-gray-500 mt-1">Bebas isi sesuai kebutuhan</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-shopping-cart text-blue-500"></i> Harga Modal/HPP (Rp)
                    </label>
                    <input type="number" name="harga_modal" id="harga_modal" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Harga beli dari supplier (opsional)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave text-green-500"></i> Harga Jual (Rp)
                    </label>
                    <input type="number" name="harga" id="harga" required min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1" id="profitInfo"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-cubes text-orange-500"></i> Stok
                    </label>
                    <input type="number" name="stok" id="stok" required min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-balance-scale text-teal-500"></i> Satuan Barang
                    </label>
                    <input type="text" name="satuan" id="satuan" value="pcs" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                           title="Satuan barang otomatis PCS (Pieces)">
                    <p class="text-xs text-gray-500 mt-1">Satuan otomatis: PCS (Pieces)</p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl font-semibold">
                    <i class="fas fa-save mr-2"></i>
                    <span id="submitButton">Simpan</span>
                </button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition-all font-semibold">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/barang-management.js') }}"></script>
@endpush
