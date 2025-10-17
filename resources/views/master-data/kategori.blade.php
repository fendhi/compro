@extends('layouts.app')

@section('title', 'Data Kategori')
@section('header', 'Master Data Kategori')

@push('styles')
<style>
    .category-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .modal { transition: opacity 0.3s ease; }
    .modal-content { animation: slideDown 0.3s ease; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<!-- Success Notification -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-lg flex items-center justify-between alert-success">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-2xl"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

@if($errors->any())
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-lg">
    <div class="flex items-center gap-3 mb-2">
        <i class="fas fa-exclamation-circle text-2xl"></i>
        <span class="font-semibold">Terjadi kesalahan:</span>
    </div>
    <ul class="list-disc list-inside ml-8">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-tags text-purple-500"></i>
                Data Kategori
                @if(!$canManage)
                    <span class="badge bg-gray-200 text-gray-700 ml-2 px-3 py-1 rounded-full text-xs">View Only</span>
                @endif
            </h2>
            <p class="text-gray-600 mt-1">Kelola kategori produk Anda</p>
        </div>
        @if($canManage)
        <button onclick="openAddModal()" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Tambah Kategori
        </button>
        @else
        <div class="text-gray-500 text-sm italic flex items-center gap-2">
            <i class="fas fa-eye"></i>
            Mode tampilan saja
        </div>
        @endif
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Total Kategori</p>
                <p class="text-3xl font-bold mt-1">{{ $kategoris->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-tags text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Total Produk</p>
                <p class="text-3xl font-bold mt-1">{{ $kategoris->sum(function($k) { return $k->barangs->count(); }) }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-box text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Kategori Aktif</p>
                <p class="text-3xl font-bold mt-1">{{ $kategoris->filter(function($k) { return $k->barangs->count() > 0; })->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <div class="relative">
        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        <input type="text" id="searchKategori" placeholder="Cari kategori..." 
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               onkeyup="filterCards()">
    </div>
</div>

<!-- Category Cards Grid -->
<div id="categoryGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($kategoris as $kategori)
    <div class="category-card bg-white rounded-xl shadow-lg overflow-hidden" 
         data-nama="{{ strtolower($kategori->nama) }}"
         data-deskripsi="{{ strtolower($kategori->deskripsi ?? '') }}">
        <!-- Color Header -->
        <div class="h-32 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center relative">
            <i class="fas fa-tag text-6xl text-white opacity-20 absolute"></i>
            <i class="fas fa-tag text-4xl text-white relative"></i>
        </div>
        
        <!-- Content -->
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center justify-between">
                <span>{{ $kategori->nama }}</span>
                <span class="text-sm font-normal bg-purple-100 text-purple-700 px-3 py-1 rounded-full">
                    {{ $kategori->barangs->count() }} item
                </span>
            </h3>
            
            @if($kategori->deskripsi)
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $kategori->deskripsi }}</p>
            @else
            <p class="text-gray-400 text-sm italic mb-4">Tidak ada deskripsi</p>
            @endif
            
            <!-- Stats -->
            <div class="flex items-center gap-4 mb-4 pt-4 border-t">
                <div class="flex-1">
                    <p class="text-xs text-gray-500">Total Stok</p>
                    <p class="text-lg font-bold text-gray-800">{{ $kategori->barangs->sum('stok') }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500">Total Nilai</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($kategori->barangs->sum(function($b) { return $b->harga * $b->stok; }) / 1000, 0) }}K</p>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-2">
                @if($canManage)
                <button onclick='openEditModal(@json($kategori))' 
                        class="flex-1 bg-blue-100 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors text-sm font-semibold">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="flex-1" onsubmit="return confirmDeleteForm(event, 'Kategori {{ $kategori->nama }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 text-red-600 px-4 py-2 rounded-lg hover:bg-red-200 transition-colors text-sm font-semibold">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
                @else
                <div class="w-full text-center text-gray-400 text-xs italic py-2">
                    <i class="fas fa-eye"></i> View Only
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-2">Belum Ada Kategori</h3>
            <p class="text-gray-500 mb-6">Mulai dengan menambahkan kategori pertama Anda</p>
            <button onclick="openAddModal()" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg">
                <i class="fas fa-plus-circle mr-2"></i>
                Tambah Kategori Pertama
            </button>
        </div>
    </div>
    @endforelse
</div>

<!-- No Results -->
<div id="noResults" class="hidden">
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-600 mb-2">Kategori Tidak Ditemukan</h3>
        <p class="text-gray-500">Coba kata kunci lain</p>
    </div>
</div>

<!-- Modal -->
<div id="kategoriModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
        <div class="sticky top-0 bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-tag"></i>
                    <span id="modalTitle">Tambah Kategori</span>
                </h3>
                <button onclick="closeModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="kategoriForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" id="methodField" name="_method" value="">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-purple-500"></i> Nama Kategori
                    </label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Contoh: Makanan, Minuman, ATK">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left text-purple-500"></i> Deskripsi (Opsional)
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Deskripsi kategori..."></textarea>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl font-semibold">
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
<script src="{{ asset('js/kategori-management.js') }}"></script>
@endpush
