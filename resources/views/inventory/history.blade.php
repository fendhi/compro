@extends('layouts.app')

@section('title', 'Riwayat Inventory')
@section('header', 'Riwayat Pergerakan Stok')

@push('styles')
<style>
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-green { background-color: #d1fae5; color: #065f46; }
    .badge-red { background-color: #fee2e2; color: #991b1b; }
    .badge-blue { background-color: #dbeafe; color: #1e40af; }
    .badge-yellow { background-color: #fef3c7; color: #92400e; }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-blue-500"></i>
                Riwayat Pergerakan Stok
            </h2>
            <p class="text-gray-600 mt-1">Lihat semua transaksi pergerakan inventory</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <form method="GET" action="{{ route('inventory.history') }}" id="filterForm" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <label class="text-xs text-gray-500 mb-1 block">Barang (auto-filter)</label>
            <select name="barang_id" id="barangFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Barang</option>
                @foreach(\App\Models\Barang::orderBy('nama')->get() as $barang)
                    <option value="{{ $barang->id }}" {{ request('barang_id') == $barang->id ? 'selected' : '' }}>
                        {{ $barang->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="text-xs text-gray-500 mb-1 block">Tipe (auto-filter)</label>
            <select name="type" id="typeFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Tipe</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                <option value="opname" {{ request('type') == 'opname' ? 'selected' : '' }}>Stock Opname</option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
            </select>
        </div>
        <div class="flex-1">
            <label class="text-xs text-gray-500 mb-1 block">Tanggal Mulai (auto-filter)</label>
            <input type="date" name="start_date" id="startDate" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tanggal Mulai">
        </div>
        <div class="flex-1">
            <label class="text-xs text-gray-500 mb-1 block">Tanggal Akhir (auto-filter)</label>
            <input type="date" name="end_date" id="endDate" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tanggal Akhir">
        </div>
        <div class="flex gap-2 items-end">
            <a href="{{ route('inventory.history') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-all text-center">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok Sebelum</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok Setelah</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Referensi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($movements as $movement)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $movement->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $movement->barang->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $movement->barang->kode_barang }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($movement->type == 'in')
                            <span class="badge badge-green">
                                <i class="fas fa-arrow-down"></i> {{ $movement->type_display }}
                            </span>
                        @elseif($movement->type == 'out')
                            <span class="badge badge-red">
                                <i class="fas fa-arrow-up"></i> {{ $movement->type_display }}
                            </span>
                        @elseif($movement->type == 'opname')
                            <span class="badge badge-blue">
                                <i class="fas fa-clipboard-check"></i> {{ $movement->type_display }}
                            </span>
                        @else
                            <span class="badge badge-yellow">
                                <i class="fas fa-edit"></i> {{ $movement->type_display }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold {{ $movement->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $movement->type == 'in' ? '+' : '-' }}{{ $movement->quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $movement->stok_before }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $movement->stok_after }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $movement->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($movement->referensi)
                            <span class="text-xs font-mono text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $movement->referensi }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada riwayat pergerakan stok</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($movements->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $movements->links() }}
    </div>
    @endif
</div>

<script>
    // Auto-submit filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filterForm');
        const barangFilter = document.getElementById('barangFilter');
        const typeFilter = document.getElementById('typeFilter');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        
        // Auto-submit when barang changes
        barangFilter.addEventListener('change', function() {
            form.submit();
        });
        
        // Auto-submit when type changes
        typeFilter.addEventListener('change', function() {
            form.submit();
        });
        
        // Auto-submit when dates change (with debounce)
        let dateChangeTimeout;
        function handleDateChange() {
            clearTimeout(dateChangeTimeout);
            
            // Submit after 500ms if at least one date is filled
            if (startDate.value || endDate.value) {
                dateChangeTimeout = setTimeout(() => {
                    form.submit();
                }, 500);
            }
        }
        
        startDate.addEventListener('change', handleDateChange);
        endDate.addEventListener('change', handleDateChange);
    });
</script>

@endsection
