@extends('layouts.app')

@section('title', 'Tambah Supplier')
@section('header', 'Pembelian - Tambah Supplier')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('supplier.index') }}" class="hover:text-[#00718F]">Data Supplier</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">Tambah Supplier</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Tambah Supplier Baru</h1>
        <p class="text-gray-600 text-sm mt-1">Isi form di bawah untuk menambahkan supplier</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
            <h2 class="text-white font-semibold text-lg">
                <i class="fas fa-truck mr-2"></i>
                Form Data Supplier
            </h2>
        </div>

        <!-- Form -->
        <form action="{{ route('supplier.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Nama Supplier -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Supplier <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_supplier" 
                        value="{{ old('nama_supplier') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent @error('nama_supplier') border-red-500 @enderror"
                        placeholder="Contoh: PT. Sumber Jaya Abadi"
                        required>
                    @error('nama_supplier')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kontak -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        No. Telepon / HP
                    </label>
                    <input 
                        type="text" 
                        name="kontak" 
                        value="{{ old('kontak') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent @error('kontak') border-red-500 @enderror"
                        placeholder="Contoh: 08123456789">
                    @error('kontak')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="Contoh: supplier@example.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Alamat
                    </label>
                    <textarea 
                        name="alamat" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent @error('alamat') border-red-500 @enderror"
                        placeholder="Masukkan alamat lengkap supplier...">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan / Catatan
                    </label>
                    <textarea 
                        name="keterangan" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent @error('keterangan') border-red-500 @enderror"
                        placeholder="Masukkan keterangan tambahan jika diperlukan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            id="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-5 h-5 text-[#00718F] border-gray-300 rounded focus:ring-[#00718F]">
                        <label for="is_active" class="flex-1 cursor-pointer">
                            <span class="text-sm font-semibold text-gray-700">Status Aktif</span>
                            <p class="text-xs text-gray-500 mt-1">Centang jika supplier ini aktif dan dapat digunakan untuk transaksi pembelian</p>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('supplier.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-[#00718F] to-[#005670] text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Supplier
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
