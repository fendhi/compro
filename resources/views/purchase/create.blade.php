@extends('layouts.app')

@section('title', 'Buat Purchase Order')
@section('header', 'Pembelian - Buat Purchase Order')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('purchase.index') }}" class="hover:text-[#00718F]">Purchase Order</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">Buat PO Baru</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Buat Purchase Order Baru</h1>
        <p class="text-gray-600 text-sm mt-1">Isi form pembelian barang dari supplier</p>
    </div>

    <!-- Error Display -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
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

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <form id="formPO" action="{{ route('purchase.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4">
                <h2 class="text-white font-semibold text-lg">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Informasi Purchase Order
                </h2>
            </div>

            <!-- Form Fields -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Tanggal PO -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal PO <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_po" 
                            value="{{ old('tanggal_po', date('Y-m-d')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent @error('tanggal_po') border-red-500 @enderror"
                            required>
                        @error('tanggal_po')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Supplier <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="supplier_id" 
                            id="supplier_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent @error('supplier_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
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
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00718F] focus:border-transparent"
                            placeholder="Tambahkan catatan untuk PO ini...">{{ old('keterangan') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- Items Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-[#00718F] to-[#005670] px-6 py-4 flex items-center justify-between">
                <h2 class="text-white font-semibold text-lg">
                    <i class="fas fa-box mr-2"></i>
                    Daftar Barang
                </h2>
                <button 
                    type="button" 
                    onclick="addItem()" 
                    class="px-4 py-2 bg-white text-[#00718F] rounded-lg hover:bg-gray-100 transition text-sm font-semibold">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Barang
                </button>
            </div>

            <!-- Items Table -->
            <div class="p-6">
                <div id="itemsContainer" class="space-y-4">
                    <!-- Items will be added here dynamically -->
                </div>

                <div id="emptyState" class="text-center py-12 text-gray-400">
                    <i class="fas fa-box-open text-6xl mb-4"></i>
                    <p class="text-lg font-medium">Belum ada barang</p>
                    <p class="text-sm mt-1">Klik "Tambah Barang" untuk mulai menambahkan</p>
                </div>

                <!-- Total Section -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-end gap-6">
                        <div class="text-right">
                            <p class="text-sm text-gray-600 mb-1">Total Item:</p>
                            <p id="totalItems" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600 mb-1">Grand Total:</p>
                            <p id="grandTotal" class="text-3xl font-bold text-[#00718F]">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('purchase.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <div class="flex gap-3">
                <button type="submit" name="action" value="draft" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Draft
                </button>
                @if(auth()->user()->role === 'owner')
                    <button type="submit" name="action" value="submit" class="px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-check-circle mr-2"></i>
                        Simpan & Setujui
                    </button>
                @else
                    <button type="submit" name="action" value="submit" class="px-6 py-2 bg-gradient-to-r from-[#00718F] to-[#005670] text-white rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Ajukan Approval
                    </button>
                @endif
            </div>
        </div>

    </form>
</div>

<!-- Modal Add New Barang -->
<div id="addBarangModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">Tambah Barang Baru</h3>
            <button type="button" onclick="closeAddBarangModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="formAddBarang" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang *</label>
                    <input type="text" id="new_nama_barang" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#00718F]" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori *</label>
                    <select id="new_kategori_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#00718F]" required>
                        <option value="">-- Pilih --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan *</label>
                    <select id="new_satuan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#00718F]" required>
                        <option value="">-- Pilih --</option>
                        <option value="pcs">Pcs</option>
                        <option value="box">Box</option>
                        <option value="kg">Kg</option>
                        <option value="liter">Liter</option>
                        <option value="meter">Meter</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Modal *</label>
                    <input type="number" id="new_harga_modal" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#00718F]" min="0" step="100" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Jual *</label>
                    <input type="number" id="new_harga_jual" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#00718F]" min="0" step="100" required>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeAddBarangModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Batal
                </button>
                <button type="button" onclick="saveNewBarang()" class="px-4 py-2 bg-[#00718F] text-white rounded-lg hover:bg-[#005670]">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
let itemCounter = 0;
let barangList = [];

// Load barang from server
async function loadBarang() {
    try {
        const response = await fetch('/api/barang-for-po');
        const data = await response.json();
        if (data.success) {
            barangList = data.data;
        }
    } catch (error) {
        console.error('Error loading barang:', error);
    }
}

// Add new item row
function addItem() {
    const container = document.getElementById('itemsContainer');
    const emptyState = document.getElementById('emptyState');
    
    itemCounter++;
    
    const itemHTML = `
        <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200" data-index="${itemCounter}">
            <div class="grid grid-cols-12 gap-4 items-start">
                <!-- Barang -->
                <div class="col-span-5">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Barang *</label>
                    <select 
                        name="items[${itemCounter}][barang_id]" 
                        class="barang-select w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#00718F]"
                        onchange="selectBarang(${itemCounter}, this)"
                        required>
                        <option value="">-- Pilih Barang --</option>
                        ${barangList.map(b => `<option value="${b.id}" data-harga="${b.harga_modal}">${b.nama} (${b.kode_barang})</option>`).join('')}
                    </select>
                    <button type="button" onclick="showAddBarangModal(${itemCounter})" class="text-xs text-[#00718F] hover:underline mt-1">
                        <i class="fas fa-plus-circle mr-1"></i>Tambah Barang Baru
                    </button>
                </div>
                
                <!-- Qty -->
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Qty *</label>
                    <input 
                        type="number" 
                        name="items[${itemCounter}][qty_order]" 
                        class="qty-input w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#00718F]"
                        min="1"
                        value="1"
                        onchange="calculateSubtotal(${itemCounter})"
                        required>
                </div>
                
                <!-- Harga -->
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Harga *</label>
                    <input 
                        type="number" 
                        name="items[${itemCounter}][harga_beli]" 
                        class="harga-input w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#00718F]"
                        min="0"
                        value="0"
                        onchange="calculateSubtotal(${itemCounter})"
                        required>
                </div>
                
                <!-- Subtotal -->
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Subtotal</label>
                    <input 
                        type="text" 
                        class="subtotal-display w-full px-3 py-2 text-sm border rounded-lg bg-gray-100 font-bold text-right"
                        value="Rp 0"
                        readonly>
                    <input type="hidden" name="items[${itemCounter}][subtotal]" class="subtotal-value" value="0">
                </div>
                
                <!-- Remove Button -->
                <div class="col-span-1 flex items-end justify-center">
                    <button 
                        type="button" 
                        onclick="removeItem(${itemCounter})"
                        class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHTML);
    emptyState.style.display = 'none';
    updateTotals();
}

// Select barang and auto-fill harga
function selectBarang(index, selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const harga = selectedOption.getAttribute('data-harga') || 0;
    const row = document.querySelector(`.item-row[data-index="${index}"]`);
    row.querySelector('.harga-input').value = harga;
    calculateSubtotal(index);
}

// Calculate subtotal for item
function calculateSubtotal(index) {
    const row = document.querySelector(`.item-row[data-index="${index}"]`);
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
    const subtotal = qty * harga;
    
    row.querySelector('.subtotal-value').value = subtotal;
    row.querySelector('.subtotal-display').value = 'Rp ' + subtotal.toLocaleString('id-ID');
    
    updateTotals();
}

// Remove item
function removeItem(index) {
    const row = document.querySelector(`.item-row[data-index="${index}"]`);
    row.remove();
    
    const container = document.getElementById('itemsContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (container.children.length === 0) {
        emptyState.style.display = 'block';
    }
    
    updateTotals();
}

// Update totals
function updateTotals() {
    const rows = document.querySelectorAll('.item-row');
    let totalItems = rows.length;
    let grandTotal = 0;
    
    rows.forEach(row => {
        const subtotal = parseFloat(row.querySelector('.subtotal-value').value) || 0;
        grandTotal += subtotal;
    });
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('grandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

// Show add barang modal
let currentItemIndex = null;
function showAddBarangModal(index) {
    currentItemIndex = index;
    document.getElementById('addBarangModal').classList.remove('hidden');
    document.getElementById('formAddBarang').reset();
}

function closeAddBarangModal() {
    document.getElementById('addBarangModal').classList.add('hidden');
    currentItemIndex = null;
}

// Save new barang
async function saveNewBarang() {
    const formData = {
        nama: document.getElementById('new_nama_barang').value,
        kategori_id: document.getElementById('new_kategori_id').value,
        satuan: document.getElementById('new_satuan').value,
        harga_modal: document.getElementById('new_harga_modal').value || 0,
        harga: document.getElementById('new_harga_jual').value || 0
    };
    
    if (!formData.nama || !formData.kategori_id || !formData.satuan) {
        alert('Nama Barang, Kategori, dan Satuan wajib diisi!');
        return;
    }
    
    if (!formData.harga_modal || !formData.harga) {
        alert('Harga Modal dan Harga Jual wajib diisi!');
        return;
    }
    
    try {
        const response = await fetch('/api/store-new-barang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Add to barangList
            barangList.push(data.barang);
            
            // Update all select dropdowns
            document.querySelectorAll('.barang-select').forEach(select => {
                const newOption = new Option(
                    `${data.barang.nama} (${data.barang.kode_barang})`,
                    data.barang.id
                );
                newOption.setAttribute('data-harga', data.barang.harga_modal || 0);
                select.add(newOption);
            });
            
            // Auto-select in current row
            if (currentItemIndex) {
                const row = document.querySelector(`.item-row[data-index="${currentItemIndex}"]`);
                row.querySelector('.barang-select').value = data.barang.id;
                selectBarang(currentItemIndex, row.querySelector('.barang-select'));
            }
            
            closeAddBarangModal();
            showNotification('success', data.message || 'Barang baru berhasil ditambahkan');
        } else {
            alert(data.message || 'Gagal menyimpan barang');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem');
    }
}

// Notification
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

// Form validation
document.getElementById('formPO').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item-row');
    
    console.log('Form submit triggered');
    console.log('Items count:', items.length);
    
    if (items.length === 0) {
        e.preventDefault();
        showNotification('error', 'Minimal tambahkan 1 barang!');
        alert('Minimal tambahkan 1 barang!');
        return false;
    }
    
    // Validate each item
    let hasError = false;
    items.forEach((row, index) => {
        const barangSelect = row.querySelector('.barang-select');
        const qtyInput = row.querySelector('.qty-input');
        const hargaInput = row.querySelector('.harga-input');
        
        if (!barangSelect.value || !qtyInput.value || !hargaInput.value || hargaInput.value == 0) {
            hasError = true;
            console.log('Item error at index:', index);
        }
    });
    
    if (hasError) {
        e.preventDefault();
        showNotification('error', 'Lengkapi semua data barang!');
        alert('Pastikan semua barang sudah dipilih, qty dan harga sudah diisi!');
        return false;
    }
    
    console.log('Form validation passed, submitting...');
    return true;
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadBarang();
});
</script>

@endsection
