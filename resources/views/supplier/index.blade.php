@extends('layouts.app')

@section('title', 'Data Supplier')
@section('header', 'Pembelian - Data Supplier')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Supplier</h1>
            <p class="text-gray-600 text-sm mt-1">Kelola data supplier untuk pembelian barang</p>
        </div>
        <a href="{{ route('supplier.create') }}" class="px-4 py-2 bg-gradient-to-r from-[#00718F] to-[#005670] text-white rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Tambah Supplier
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('supplier.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select 
                    name="status" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Supplier</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nama, kontak, atau email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#00718F] text-white rounded-lg hover:bg-[#005670] transition">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('supplier.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Supplier -->
        <div class="bg-gradient-to-r from-[#00718F] to-[#005670] text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Total Supplier</p>
                    <h2 class="text-3xl font-bold">{{ $suppliers->total() }}</h2>
                </div>
                <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Aktif -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">Supplier Aktif</p>
                    <h2 class="text-3xl font-bold">{{ $suppliers->where('is_active', true)->count() }}</h2>
                </div>
                <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Tidak Aktif -->
        <div class="bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-sm mb-1">Tidak Aktif</p>
                    <h2 class="text-3xl font-bold">{{ $suppliers->where('is_active', false)->count() }}</h2>
                </div>
                <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
            <h2 class="text-white font-semibold text-lg">
                <i class="fas fa-list mr-2"></i>
                Daftar Supplier
            </h2>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kontak</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Alamat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($suppliers as $index => $supplier)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- No -->
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $index + 1 }}
                        </td>

                        <!-- Nama Supplier -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $supplier->nama_supplier }}
                            </div>
                            @if($supplier->keterangan)
                            <div class="text-xs text-gray-500">{{ Str::limit($supplier->keterangan, 50) }}</div>
                            @endif
                        </td>

                        <!-- Kontak -->
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $supplier->kontak ?? '-' }}
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $supplier->email ?? '-' }}
                        </td>

                        <!-- Alamat -->
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $supplier->alamat ? Str::limit($supplier->alamat, 50) : '-' }}
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            @if($supplier->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Edit -->
                                <a href="{{ route('supplier.edit', $supplier->id) }}" 
                                   class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Toggle Status -->
                                <button onclick="toggleStatus({{ $supplier->id }})"
                                   class="p-2 {{ $supplier->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} rounded-lg transition"
                                   title="{{ $supplier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas fa-{{ $supplier->is_active ? 'ban' : 'check' }}"></i>
                                </button>

                                <!-- Delete -->
                                <button onclick="deleteSupplier({{ $supplier->id }})"
                                   class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-truck text-6xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada data supplier</p>
                                <p class="text-sm mt-1">Tambahkan supplier baru untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($suppliers->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $suppliers->links() }}
        </div>
        @endif

    </div>
</div>

<!-- JavaScript -->
<script>
function toggleStatus(id) {
    if (!confirm('Apakah Anda yakin ingin mengubah status supplier ini?')) return;

    fetch(`/supplier/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        showNotification('error', 'Terjadi kesalahan sistem');
    });
}

function deleteSupplier(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus supplier ini?\nSupplier yang memiliki riwayat pembelian tidak dapat dihapus.')) return;

    fetch(`/supplier/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        showNotification('error', 'Terjadi kesalahan sistem');
    });
}

function showNotification(type, message) {
    // Menggunakan notification yang sudah ada di layout
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
</script>

@if(session('success'))
<script>
    showNotification('success', '{{ session('success') }}');
</script>
@endif

@if(session('error'))
<script>
    showNotification('error', '{{ session('error') }}');
</script>
@endif

@endsection
