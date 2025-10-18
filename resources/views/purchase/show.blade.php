@extends('layouts.app')

@section('title', 'Detail Purchase Order')
@section('header', 'Pembelian - Detail Purchase Order')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('purchase.index') }}" class="hover:text-[#00718F]">Purchase Order</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">{{ $purchase->kode_po }}</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Purchase Order</h1>
                <p class="text-gray-600 text-sm mt-1">{{ $purchase->kode_po }}</p>
            </div>
            
            <!-- Status Badge -->
            <div>
                @if($purchase->status == 'draft')
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-gray-100 text-gray-800">
                        <i class="fas fa-file mr-2"></i>DRAFT
                    </span>
                @elseif($purchase->status == 'pending')
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-2"></i>PENDING APPROVAL
                    </span>
                @elseif($purchase->status == 'approved')
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>DISETUJUI
                    </span>
                @elseif($purchase->status == 'rejected')
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-2"></i>DITOLAK
                    </span>
                @elseif($purchase->status == 'completed')
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-blue-100 text-blue-800">
                        <i class="fas fa-check-double mr-2"></i>SELESAI
                    </span>
                @elseif($purchase->status == 'cancelled')
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-gray-100 text-gray-600">
                        <i class="fas fa-ban mr-2"></i>DIBATALKAN
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-6 flex items-center gap-3">
        <!-- Edit (Draft only) -->
        @if($purchase->canEdit())
        <a href="{{ route('purchase.edit', $purchase->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
            <i class="fas fa-edit mr-2"></i>
            Edit PO
        </a>
        @endif

        <!-- Submit (Draft + Admin/Owner) -->
        @if($purchase->canSubmit())
        <button onclick="submitPO()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-paper-plane mr-2"></i>
            Ajukan Approval
        </button>
        @endif

        <!-- Approve/Reject (Pending + Owner) -->
        @if($purchase->canApprove())
        <button onclick="showApprovalModal('approve')" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-check mr-2"></i>
            Setujui
        </button>
        <button onclick="showApprovalModal('reject')" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
            <i class="fas fa-times mr-2"></i>
            Tolak
        </button>
        @endif

        <!-- Confirm (Approved + Owner) -->
        @if($purchase->canConfirm())
        <button onclick="confirmReceived()" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
            <i class="fas fa-box mr-2"></i>
            Konfirmasi Terima Barang
        </button>
        @endif

        <!-- Export PDF -->
        <a href="{{ route('purchase.pdf', $purchase->id) }}" target="_blank" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition inline-flex items-center">
            <i class="fas fa-file-pdf mr-2"></i>
            Export PDF
        </a>

        <!-- Print -->
        <button onclick="window.print()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-print mr-2"></i>
            Cetak
        </button>

        <!-- Delete (Draft only) -->
        @if($purchase->canDelete())
        <button onclick="deletePO()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
            <i class="fas fa-trash mr-2"></i>
            Hapus
        </button>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Informasi PO -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
                    <h2 class="text-white font-semibold text-lg">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Purchase Order
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Kode PO</p>
                        <p class="text-sm font-bold text-gray-800">{{ $purchase->kode_po }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal PO</p>
                        <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($purchase->tanggal_po)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Supplier</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $purchase->supplier->nama_supplier }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Kontak Supplier</p>
                        <p class="text-sm text-gray-800">{{ $purchase->supplier->kontak ?? '-' }}</p>
                    </div>
                    @if($purchase->keterangan)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Keterangan</p>
                        <p class="text-sm text-gray-700">{{ $purchase->keterangan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Daftar Barang -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
                    <h2 class="text-white font-semibold text-lg">
                        <i class="fas fa-box mr-2"></i>
                        Daftar Barang ({{ $purchase->details->count() }} item)
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Barang</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($purchase->details as $index => $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-900">{{ $detail->barang->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $detail->barang->kode_barang }}</div>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">
                                    {{ $detail->qty_order }} {{ $detail->barang->satuan }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">
                                    Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-right text-sm font-bold text-gray-700">GRAND TOTAL:</td>
                                <td class="px-4 py-4 text-right text-lg font-bold text-[#00718F]">
                                    Rp {{ number_format($purchase->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column (1/3 width) -->
        <div class="space-y-6">
            
            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
                    <h2 class="text-white font-semibold text-lg">
                        <i class="fas fa-history mr-2"></i>
                        Timeline
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Created -->
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">Dibuat</p>
                                <p class="text-xs text-gray-600">{{ $purchase->creator->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $purchase->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Submitted -->
                        @if($purchase->submitted_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-paper-plane text-yellow-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">Diajukan</p>
                                <p class="text-xs text-gray-600">{{ $purchase->creator->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($purchase->submitted_at)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Approved/Rejected -->
                        @if($purchase->approved_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 {{ $purchase->status == 'approved' || $purchase->status == 'completed' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $purchase->status == 'approved' || $purchase->status == 'completed' ? 'check' : 'times' }} text-{{ $purchase->status == 'approved' || $purchase->status == 'completed' ? 'green' : 'red' }}-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">{{ $purchase->status == 'rejected' ? 'Ditolak' : 'Disetujui' }}</p>
                                <p class="text-xs text-gray-600">{{ $purchase->approver->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($purchase->approved_at)->format('d/m/Y H:i') }}</p>
                                @if($purchase->rejection_reason)
                                <p class="text-xs text-red-600 mt-1 italic">"{{ $purchase->rejection_reason }}"</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Completed -->
                        @if($purchase->status == 'completed' && $purchase->confirmed_by)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-box text-purple-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">Barang Diterima</p>
                                <p class="text-xs text-gray-600">{{ $purchase->confirmer->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $purchase->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval History -->
            @if($purchase->approvals->count() > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
                    <h2 class="text-white font-semibold text-lg">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Riwayat Approval
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($purchase->approvals as $approval)
                        <div class="border-l-4 {{ $approval->action == 'approved' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50' }} p-3 rounded-r">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold {{ $approval->action == 'approved' ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $approval->action == 'approved' ? 'APPROVED' : 'REJECTED' }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $approval->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-xs font-semibold text-gray-700">{{ $approval->approver->name ?? '-' }}</p>
                            @if($approval->notes)
                            <p class="text-xs text-gray-600 mt-1 italic">"{{ $approval->notes }}"</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
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

<!-- Confirm Received Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Konfirmasi Penerimaan Barang</h3>
            <button onclick="closeConfirmModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5 mr-3"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Masukkan jumlah barang yang benar-benar diterima</li>
                        <li>Stok akan otomatis bertambah sesuai qty yang diterima</li>
                        <li>Jika qty diterima kurang dari order, status akan jadi "Partial"</li>
                    </ul>
                </div>
            </div>
        </div>

        <form id="confirmForm">
            <div class="space-y-4">
                @foreach($purchase->details as $index => $detail)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-5">
                            <p class="font-semibold text-gray-800">{{ $detail->barang->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $detail->barang->kode_barang }}</p>
                        </div>
                        <div class="col-span-2 text-center">
                            <p class="text-xs text-gray-600">Qty Order</p>
                            <p class="font-bold text-gray-800">{{ $detail->qty_order }}</p>
                        </div>
                        <div class="col-span-2 text-center">
                            <p class="text-xs text-gray-600">Sudah Diterima</p>
                            <p class="font-bold text-blue-600">{{ $detail->qty_received }}</p>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Terima Sekarang *</label>
                            <input 
                                type="number" 
                                name="items[{{ $index }}][detail_id]" 
                                value="{{ $detail->id }}" 
                                hidden>
                            <input 
                                type="number" 
                                name="items[{{ $index }}][qty_received]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                min="0"
                                max="{{ $detail->qty_order - $detail->qty_received }}"
                                value="{{ $detail->qty_order - $detail->qty_received }}"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Sisa: {{ $detail->qty_order - $detail->qty_received }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeConfirmModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                    <i class="fas fa-check mr-2"></i>
                    Konfirmasi Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
function submitPO() {
    if (!confirm('Ajukan PO ini untuk disetujui?')) return;

    fetch('{{ route("purchase.submit", $purchase->id) }}', {
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

function showApprovalModal(action) {
    const modal = document.getElementById('approvalModal');
    const form = document.getElementById('approvalForm');
    const title = document.getElementById('modalTitle');
    const btn = document.getElementById('modalSubmitBtn');

    if (action === 'approve') {
        title.textContent = 'Setujui Purchase Order';
        btn.textContent = 'Setujui';
        btn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
        form.action = '{{ route("purchase.approve", $purchase->id) }}';
    } else {
        title.textContent = 'Tolak Purchase Order';
        btn.textContent = 'Tolak';
        btn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
        form.action = '{{ route("purchase.reject", $purchase->id) }}';
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('approvalNotes').value = '';
}

function confirmReceived() {
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}

// Handle confirm form submit
document.getElementById('confirmForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!confirm('Konfirmasi bahwa barang telah diterima?\nStok akan otomatis bertambah.')) return;
    
    const formData = new FormData(this);
    const items = [];
    
    // Parse form data
    for (let i = 0; i < {{ count($purchase->details) }}; i++) {
        const detailId = formData.get(`items[${i}][detail_id]`);
        const qtyReceived = formData.get(`items[${i}][qty_received]`);
        
        if (detailId && qtyReceived) {
            items.push({
                detail_id: parseInt(detailId),
                qty_received: parseInt(qtyReceived)
            });
        }
    }
    
    console.log('Sending items:', items);

    fetch('{{ route("purchase.confirm", $purchase->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ items: items })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan sistem');
    });
});

function deletePO() {
    if (!confirm('Hapus PO ini?')) return;

    fetch('{{ route("purchase.destroy", $purchase->id) }}', {
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
            setTimeout(() => window.location.href = '{{ route("purchase.index") }}', 1000);
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
