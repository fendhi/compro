@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('header', 'Pembelian - Edit Supplier')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('supplier.index') }}" class="hover:text-[#00718F]">Data Supplier</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">Edit Supplier</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Data Supplier</h1>
        <p class="text-gray-600 text-sm mt-1">Perbarui informasi supplier</p>
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
        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Nama Supplier -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Supplier <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_supplier" 
                        value="{{ old('nama_supplier', $supplier->nama_supplier) }}"
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
                        value="{{ old('kontak', $supplier->kontak) }}"
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
                        value="{{ old('email', $supplier->email) }}"
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
                        placeholder="Masukkan alamat lengkap supplier...">{{ old('alamat', $supplier->alamat) }}</textarea>
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
                        placeholder="Masukkan keterangan tambahan jika diperlukan...">{{ old('keterangan', $supplier->keterangan) }}</textarea>
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
                            {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
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
                    Update Supplier
                </button>
            </div>

        </form>

    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
            <div>
                <h3 class="text-sm font-semibold text-blue-800 mb-1">Informasi</h3>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li>• Supplier yang memiliki riwayat pembelian tidak dapat dinonaktifkan</li>
                    <li>• Pastikan data kontak supplier selalu ter-update untuk komunikasi yang baik</li>
                    <li>• Status tidak aktif membuat supplier tidak muncul di form pembelian baru</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
