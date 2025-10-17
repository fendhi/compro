@extends('layouts.app')

@section('title', 'Input Pengeluaran')
@section('header', 'Keuangan - Input Pengeluaran')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Input Pengeluaran</h2>
            <p class="text-gray-600 mt-1">Catat pengeluaran operasional bisnis Anda</p>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-[#00718F] to-[#005670] text-white px-6 py-4">
        <h3 class="text-xl font-bold">Form Input Pengeluaran</h3>
    </div>
    
    <div class="p-6">
            <form id="formPengeluaran" action="{{ route('pengeluaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Left Column -->
                    <div class="space-y-4">
                        
                        <!-- Tanggal -->
                        <div>
                            <label for="tanggalInput" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="tanggalInput" 
                                name="tanggal" 
                                value="{{ old('tanggal', date('Y-m-d')) }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent"
                                required>
                            @error('tanggal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="kategoriInput" class="block text-sm font-semibold text-gray-700 mb-2">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="kategoriInput" 
                                name="kategori_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent"
                                required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">
                                        @if($category->parent)
                                            {{ $category->parent->nama }} - {{ $category->nama }}
                                        @else
                                            {{ $category->nama }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsiInput" class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Pengeluaran <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="deskripsiInput" 
                                name="deskripsi" 
                                value="{{ old('deskripsi') }}"
                                placeholder="Contoh: Beli ATK, Bayar listrik, Gaji karyawan"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent"
                                required>
                            @error('deskripsi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nominal -->
                        <div>
                            <label for="nominalInput" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nominal <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 font-semibold">Rp</span>
                                <input 
                                    type="text" 
                                    id="nominalInput" 
                                    name="nominal"
                                    placeholder="0"
                                    class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent"
                                    required>
                            </div>
                            @error('nominal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Format otomatis: 1.000.000</p>
                        </div>

                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        
                        <!-- Metode Pembayaran -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Metode Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Cash -->
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-[#00718F] transition">
                                    <input 
                                        type="radio" 
                                        name="metode_pembayaran" 
                                        value="cash" 
                                        class="hidden peer"
                                        required>
                                    <div class="peer-checked:bg-[#00718F] peer-checked:border-[#00718F] w-full text-center">
                                        <i class="fas fa-money-bill-wave text-2xl mb-2 peer-checked:text-white"></i>
                                        <p class="font-semibold peer-checked:text-white">Cash</p>
                                    </div>
                                </label>

                                <!-- Transfer -->
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-[#00718F] transition">
                                    <input 
                                        type="radio" 
                                        name="metode_pembayaran" 
                                        value="transfer" 
                                        class="hidden peer"
                                        required>
                                    <div class="peer-checked:bg-[#00718F] peer-checked:border-[#00718F] w-full text-center">
                                        <i class="fas fa-university text-2xl mb-2 peer-checked:text-white"></i>
                                        <p class="font-semibold peer-checked:text-white">Transfer</p>
                                    </div>
                                </label>

                                <!-- QRIS -->
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-[#00718F] transition">
                                    <input 
                                        type="radio" 
                                        name="metode_pembayaran" 
                                        value="qris" 
                                        class="hidden peer"
                                        required>
                                    <div class="peer-checked:bg-[#00718F] peer-checked:border-[#00718F] w-full text-center">
                                        <i class="fas fa-qrcode text-2xl mb-2 peer-checked:text-white"></i>
                                        <p class="font-semibold peer-checked:text-white">QRIS</p>
                                    </div>
                                </label>

                                <!-- E-Wallet -->
                                <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-[#00718F] transition">
                                    <input 
                                        type="radio" 
                                        name="metode_pembayaran" 
                                        value="ewallet" 
                                        class="hidden peer"
                                        required>
                                    <div class="peer-checked:bg-[#00718F] peer-checked:border-[#00718F] w-full text-center">
                                        <i class="fas fa-mobile-alt text-2xl mb-2 peer-checked:text-white"></i>
                                        <p class="font-semibold peer-checked:text-white">E-Wallet</p>
                                    </div>
                                </label>
                            </div>
                            @error('metode_pembayaran')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload Bukti -->
                        <div>
                            <label for="buktiInput" class="block text-sm font-semibold text-gray-700 mb-2">
                                Upload Bukti Pembayaran
                                <span class="text-gray-500 font-normal">(Opsional)</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-[#00718F] transition cursor-pointer" id="uploadArea">
                                <input 
                                    type="file" 
                                    id="buktiInput" 
                                    name="bukti" 
                                    accept="image/*"
                                    class="hidden">
                                <label for="buktiInput" class="cursor-pointer" id="uploadLabel">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600">Klik untuk upload gambar</p>
                                    <p class="text-xs text-gray-400 mt-1">Max 2MB (JPG, PNG)</p>
                                </label>
                            </div>
                            @error('bukti')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3 hidden">
                                <div class="relative inline-block">
                                    <img id="previewImg" src="" class="rounded-lg shadow-md max-h-48">
                                    <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="catatanInput" class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan Tambahan
                                <span class="text-gray-500 font-normal">(Opsional)</span>
                            </label>
                            <textarea 
                                id="catatanInput" 
                                name="catatan" 
                                rows="4"
                                placeholder="Tambahkan catatan jika diperlukan..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent resize-none">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3 mt-6 pt-6 border-t">
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-[#00718F] to-[#005670] text-white py-3 rounded-lg hover:from-[#005670] hover:to-[#004050] transition-all shadow-lg hover:shadow-xl font-semibold">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Pengeluaran
                    </button>
                    <a 
                        href="{{ route('pengeluaran.index') }}" 
                        class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition-all font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-[#E6F3F6] border border-[#00718F] rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-[#00718F] text-xl mt-0.5"></i>
            <div>
                <h3 class="font-semibold text-[#00718F] mb-1">Tips Pencatatan Pengeluaran</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Catat pengeluaran sehari sekali untuk akurasi data</li>
                    <li>• Simpan bukti pembayaran untuk audit keuangan</li>
                    <li>• Gunakan deskripsi yang jelas dan spesifik</li>
                    <li>• Pilih kategori yang sesuai untuk laporan yang akurat</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Include expense-management.js -->
<script src="{{ asset('js/expense-management.js') }}"></script>

<!-- Custom styles for radio buttons -->
<style>
    input[type="radio"]:checked + div {
        background-color: #00718F !important;
        border-color: #00718F !important;
        color: white !important;
    }
    
    input[type="radio"]:checked + div i,
    input[type="radio"]:checked + div p {
        color: white !important;
    }
</style>
@endsection
