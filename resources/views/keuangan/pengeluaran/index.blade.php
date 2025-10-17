@extends('layouts.app')

@section('title', 'List Pengeluaran')
@section('header', 'Keuangan - Data Pengeluaran')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">List Pengeluaran</h1>
            <p class="text-gray-600 text-sm mt-1">Riwayat semua pengeluaran operasional</p>
        </div>
        <a href="{{ route('pengeluaran.create') }}" class="px-4 py-2 bg-gradient-to-r from-[#00718F] to-[#005670] text-white rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Tambah Pengeluaran
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('pengeluaran.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ request('start_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
            </div>

            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Akhir</label>
                <input 
                    type="date" 
                    name="end_date" 
                    value="{{ request('end_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                <select 
                    name="kategori_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                <select 
                    name="metode" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('metode') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="qris" {{ request('metode') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="ewallet" {{ request('metode') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
            </div>

            <!-- Search -->
            <div class="md:col-span-3">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Deskripsi</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari berdasarkan deskripsi atau catatan..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#00718F] text-white rounded-lg hover:bg-[#005670] transition">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('pengeluaran.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm mb-1">Total Pengeluaran</p>
                <h2 class="text-3xl font-bold">{{ 'Rp ' . number_format($totalPengeluaran) }}</h2>
                <p class="text-red-100 text-sm mt-1">{{ $expenses->total() }} transaksi</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-down text-4xl"></i>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
            <h2 class="text-white font-semibold text-lg">
                <i class="fas fa-list mr-2"></i>
                Daftar Pengeluaran
            </h2>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Deskripsi</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Nominal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Metode</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Bukti</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($expenses as $index => $expense)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- No -->
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ ($expenses->currentPage() - 1) * $expenses->perPage() + $index + 1 }}
                        </td>

                        <!-- Tanggal -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $expense->tanggal->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $expense->tanggal->diffForHumans() }}
                            </div>
                        </td>

                        <!-- Kategori -->
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                                  style="background-color: {{ $expense->kategori->warna }}20; color: {{ $expense->kategori->warna }}; border: 1px solid {{ $expense->kategori->warna }}">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                {{ $expense->kategori->nama }}
                            </span>
                        </td>

                        <!-- Deskripsi -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $expense->deskripsi }}
                            </div>
                            @if($expense->catatan)
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-sticky-note mr-1"></i>
                                {{ Str::limit($expense->catatan, 50) }}
                            </div>
                            @endif
                        </td>

                        <!-- Nominal -->
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-bold text-red-600">
                                {{ 'Rp ' . number_format($expense->nominal) }}
                            </div>
                        </td>

                        <!-- Metode -->
                        <td class="px-6 py-4 text-center">
                            @if($expense->metode_pembayaran == 'cash')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Cash
                                </span>
                            @elseif($expense->metode_pembayaran == 'transfer')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-university mr-1"></i> Transfer
                                </span>
                            @elseif($expense->metode_pembayaran == 'qris')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-qrcode mr-1"></i> QRIS
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-mobile-alt mr-1"></i> E-Wallet
                                </span>
                            @endif
                        </td>

                        <!-- Bukti -->
                        <td class="px-6 py-4 text-center">
                            @if($expense->bukti_path)
                                <button 
                                    onclick="showBukti('{{ $expense->bukti_url }}')" 
                                    class="text-[#00718F] hover:text-[#005670] transition">
                                    <i class="fas fa-image text-lg"></i>
                                </button>
                            @else
                                <span class="text-gray-400">
                                    <i class="fas fa-minus"></i>
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button 
                                    onclick="editExpense({{ $expense->id }})" 
                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button 
                                    onclick="deleteExpense({{ $expense->id }})" 
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-inbox text-6xl mb-4"></i>
                                <p class="text-lg font-semibold">Belum Ada Data Pengeluaran</p>
                                <p class="text-sm mt-2">Klik tombol "Tambah Pengeluaran" untuk mulai mencatat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($expenses->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $expenses->links() }}
        </div>
        @endif

    </div>
</div>

<!-- Modal Preview Bukti -->
<div id="buktiModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="closeBukti()">
    <div class="relative max-w-4xl max-h-screen p-4" onclick="event.stopPropagation()">
        <button 
            onclick="closeBukti()" 
            class="absolute top-6 right-6 w-10 h-10 bg-white rounded-full flex items-center justify-center hover:bg-gray-200 transition z-10">
            <i class="fas fa-times text-gray-700"></i>
        </button>
        <img id="buktiImage" src="" class="max-w-full max-h-screen rounded-lg shadow-2xl">
    </div>
</div>

<!-- Include expense-management.js -->
<script src="{{ asset('js/expense-management.js') }}"></script>

<!-- Modal functions -->
<script>
    function showBukti(url) {
        document.getElementById('buktiImage').src = url;
        document.getElementById('buktiModal').classList.remove('hidden');
        document.getElementById('buktiModal').classList.add('flex');
    }

    function closeBukti() {
        document.getElementById('buktiModal').classList.add('hidden');
        document.getElementById('buktiModal').classList.remove('flex');
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeBukti();
        }
    });
</script>

@endsection
