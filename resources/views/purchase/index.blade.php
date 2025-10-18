@extends('layouts.app')

@section('title', 'Purchase Order')
@section('header', 'Pembelian - Purchase Order')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Purchase Order</h1>
            <p class="text-gray-600 text-sm mt-1">Kelola pembelian barang dari supplier</p>
        </div>
        <a href="{{ route('purchase.create') }}" class="px-4 py-2 bg-gradient-to-r from-[#00718F] to-[#005670] text-white rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Buat PO Baru
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('purchase.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Supplier -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Supplier</label>
                <select 
                    name="supplier_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select 
                    name="status" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                <input 
                    type="date" 
                    name="tanggal_dari" 
                    value="{{ request('tanggal_dari') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent">
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#00718F] text-white rounded-lg hover:bg-[#005670] transition">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('purchase.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <!-- Total PO -->
        <div class="bg-gradient-to-r from-[#00718F] to-[#005670] text-white rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs mb-1">Total PO</p>
                    <h2 class="text-2xl font-bold">{{ $purchases->total() }}</h2>
                </div>
                <i class="fas fa-file-invoice text-3xl opacity-50"></i>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-xs mb-1">Pending</p>
                    <h2 class="text-2xl font-bold">{{ $statusCounts['pending'] ?? 0 }}</h2>
                </div>
                <i class="fas fa-clock text-3xl opacity-50"></i>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-xs mb-1">Disetujui</p>
                    <h2 class="text-2xl font-bold">{{ $statusCounts['approved'] ?? 0 }}</h2>
                </div>
                <i class="fas fa-check-circle text-3xl opacity-50"></i>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs mb-1">Selesai</p>
                    <h2 class="text-2xl font-bold">{{ $statusCounts['completed'] ?? 0 }}</h2>
                </div>
                <i class="fas fa-check-double text-3xl opacity-50"></i>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-xs mb-1">Ditolak</p>
                    <h2 class="text-2xl font-bold">{{ $statusCounts['rejected'] ?? 0 }}</h2>
                </div>
                <i class="fas fa-times-circle text-3xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Notification for Owner -->
    @if(auth()->user()->hasRole('owner') && ($statusCounts['pending'] ?? 0) > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
            <div>
                <p class="text-sm font-semibold text-yellow-800">
                    Ada {{ $statusCounts['pending'] }} PO yang menunggu persetujuan Anda!
                </p>
                <p class="text-xs text-yellow-700 mt-1">Silakan tinjau dan setujui/tolak purchase order tersebut.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
            <h2 class="text-white font-semibold text-lg">
                <i class="fas fa-list mr-2"></i>
                Daftar Purchase Order
            </h2>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode PO</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Supplier</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($purchases as $index => $purchase)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- No -->
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ ($purchases->currentPage() - 1) * $purchases->perPage() + $index + 1 }}
                        </td>

                        <!-- Kode PO -->
                        <td class="px-4 py-3">
                            <a href="{{ route('purchase.show', $purchase->id) }}" class="text-sm font-semibold text-[#00718F] hover:text-[#005670]">
                                {{ $purchase->kode_po }}
                            </a>
                        </td>

                        <!-- Tanggal -->
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($purchase->tanggal_po)->format('d/m/Y') }}
                        </td>

                        <!-- Supplier -->
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $purchase->supplier->nama_supplier }}
                        </td>

                        <!-- Total -->
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                            Rp {{ number_format($purchase->total_harga, 0, ',', '.') }}
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-3 text-center">
                            @if($purchase->status == 'draft')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-file mr-1"></i>Draft
                                </span>
                            @elseif($purchase->status == 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            @elseif($purchase->status == 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Disetujui
                                </span>
                            @elseif($purchase->status == 'rejected')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Ditolak
                                </span>
                            @elseif($purchase->status == 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-check-double mr-1"></i>Selesai
                                </span>
                            @elseif($purchase->status == 'cancelled')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    <i class="fas fa-ban mr-1"></i>Dibatalkan
                                </span>
                            @endif
                        </td>

                        <!-- Dibuat Oleh -->
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $purchase->creator->name ?? '-' }}
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <!-- View -->
                                <a href="{{ route('purchase.show', $purchase->id) }}" 
                                   class="p-1.5 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition text-xs"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit (Draft only) -->
                                @if($purchase->canEdit())
                                <a href="{{ route('purchase.edit', $purchase->id) }}" 
                                   class="p-1.5 bg-yellow-100 text-yellow-600 rounded hover:bg-yellow-200 transition text-xs"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                <!-- Submit (Draft + Admin/Owner) -->
                                @if($purchase->canSubmit())
                                <button onclick="submitPO({{ $purchase->id }})"
                                   class="p-1.5 bg-green-100 text-green-600 rounded hover:bg-green-200 transition text-xs"
                                   title="Ajukan">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                @endif

                                <!-- Approve/Reject (Pending + Owner) -->
                                @if($purchase->canApprove())
                                <button onclick="showApprovalModal({{ $purchase->id }}, 'approve')"
                                   class="p-1.5 bg-green-100 text-green-600 rounded hover:bg-green-200 transition text-xs"
                                   title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="showApprovalModal({{ $purchase->id }}, 'reject')"
                                   class="p-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200 transition text-xs"
                                   title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif

                                <!-- Confirm (Approved + Owner) -->
                                @if($purchase->canConfirm())
                                <button onclick="confirmReceived({{ $purchase->id }})"
                                   class="p-1.5 bg-purple-100 text-purple-600 rounded hover:bg-purple-200 transition text-xs"
                                   title="Konfirmasi Terima">
                                    <i class="fas fa-box"></i>
                                </button>
                                @endif

                                <!-- Delete (Draft only) -->
                                @if($purchase->canDelete())
                                <button onclick="deletePO({{ $purchase->id }})"
                                   class="p-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200 transition text-xs"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-file-invoice text-6xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada Purchase Order</p>
                                <p class="text-sm mt-1">Buat PO baru untuk memulai pembelian</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($purchases->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $purchases->links() }}
        </div>
        @endif

    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-xl font-bold mb-4" id="modalTitle">Approve PO</h3>
        <form id="approvalForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea 
                    name="notes" 
                    id="approvalNotes"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F]"
                    placeholder="Tambahkan catatan (opsional)..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" id="modalSubmitBtn" class="px-4 py-2 bg-[#00718F] text-white rounded-lg hover:bg-[#005670]">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
function submitPO(id) {
    if (!confirm('Ajukan PO ini untuk disetujui?')) return;

    fetch(`/purchase/${id}/submit`, {
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
    .catch(() => showNotification('error', 'Terjadi kesalahan'));
}

function showApprovalModal(id, action) {
    const modal = document.getElementById('approvalModal');
    const form = document.getElementById('approvalForm');
    const title = document.getElementById('modalTitle');
    const btn = document.getElementById('modalSubmitBtn');

    if (action === 'approve') {
        title.textContent = 'Setujui Purchase Order';
        btn.textContent = 'Setujui';
        btn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
        form.action = `/purchase/${id}/approve`;
    } else {
        title.textContent = 'Tolak Purchase Order';
        btn.textContent = 'Tolak';
        btn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
        form.action = `/purchase/${id}/reject`;
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('approvalNotes').value = '';
}

function confirmReceived(id) {
    if (!confirm('Konfirmasi bahwa barang telah diterima?\nStok akan otomatis bertambah.')) return;

    fetch(`/purchase/${id}/confirm`, {
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
    .catch(() => showNotification('error', 'Terjadi kesalahan'));
}

function deletePO(id) {
    if (!confirm('Hapus PO ini?')) return;

    fetch(`/purchase/${id}`, {
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
    .catch(() => showNotification('error', 'Terjadi kesalahan'));
}

function showNotification(type, message) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Handle form submit for modal
document.getElementById('approvalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(() => showNotification('error', 'Terjadi kesalahan'));
});
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
