// POS JavaScript - Shopping Cart Management with Discount Support
let cart = [];

// Add product to cart
function addToCart(element) {
  const id = element.dataset.id;
  const nama = element.dataset.nama;
  const harga = parseFloat(element.dataset.harga);
  const stok = parseInt(element.dataset.stok);

  const existingItem = cart.find((item) => item.id == id);

  if (existingItem) {
    if (existingItem.jumlah < stok) {
      existingItem.jumlah++;
    } else {
      alert("⚠️ Stok tidak mencukupi!");
      return;
    }
  } else {
    if (stok > 0) {
      cart.push({
        id: id,
        nama: nama,
        harga: harga,
        jumlah: 1,
        stok: stok,
        diskon_persen: 0, // NEW: Diskon per item
      });
    } else {
      alert("⚠️ Stok habis!");
      return;
    }
  }

  renderCart();
  updateTotal();
}

// Render cart items
function renderCart() {
  const cartItems = document.getElementById("cartItems");

  if (cart.length === 0) {
    cartItems.innerHTML = `
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-shopping-basket text-4xl mb-2"></i>
                <p class="text-sm">Keranjang masih kosong</p>
            </div>
        `;
    return;
  }

  cartItems.innerHTML = cart
    .map((item, index) => {
      const hargaAsli = item.harga * item.jumlah;
      const diskonAmount = hargaAsli * (item.diskon_persen / 100);
      const hargaSetelahDiskon = hargaAsli - diskonAmount;
      const hargaSatuan = item.harga - item.harga * (item.diskon_persen / 100);

      return `
        <div class="cart-item p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-200">
            <div class="flex items-start justify-between mb-2">
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">${item.nama}</p>
                    <div class="flex items-center gap-2 mt-1">
                        ${
                          item.diskon_persen > 0
                            ? `
                            <p class="text-xs text-gray-400 line-through">Rp ${item.harga.toLocaleString("id-ID")}</p>
                            <p class="text-xs text-green-600 font-semibold">Rp ${hargaSatuan.toLocaleString("id-ID")}</p>
                            <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded">-${item.diskon_persen}%</span>
                        `
                            : `
                            <p class="text-xs text-gray-600">Rp ${item.harga.toLocaleString("id-ID")}</p>
                        `
                        }
                        <p class="text-xs text-gray-500">× ${item.jumlah}</p>
                    </div>
                </div>
                <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-600 transition-colors ml-2">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </div>
            
            <!-- Quantity Controls -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <button onclick="updateQuantity(${index}, -1)" class="w-7 h-7 bg-red-500 text-white rounded hover:bg-red-600 flex items-center justify-center transition-colors">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="w-10 text-center font-bold text-gray-800">${item.jumlah}</span>
                    <button onclick="updateQuantity(${index}, 1)" class="w-7 h-7 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                
                <!-- Discount Input -->
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600">Diskon:</label>
                    <input type="number" 
                           value="${item.diskon_persen}" 
                           min="0" 
                           max="100" 
                           class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500"
                           onchange="updateItemDiscount(${index}, this.value)"
                           placeholder="0">
                    <span class="text-xs text-gray-500">%</span>
                </div>
            </div>
            
            <!-- Subtotal -->
            <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                <span class="text-xs text-gray-600">Subtotal:</span>
                <div class="text-right">
                    ${
                      item.diskon_persen > 0
                        ? `
                        <p class="text-xs text-gray-400 line-through">Rp ${hargaAsli.toLocaleString("id-ID")}</p>
                        <p class="text-sm font-bold text-blue-600">Rp ${hargaSetelahDiskon.toLocaleString("id-ID")}</p>
                    `
                        : `
                        <p class="text-sm font-bold text-blue-600">Rp ${hargaAsli.toLocaleString("id-ID")}</p>
                    `
                    }
                </div>
            </div>
        </div>
        `;
    })
    .join("");
}

// Update item discount
function updateItemDiscount(index, diskon) {
  const diskonValue = parseFloat(diskon) || 0;
  if (diskonValue < 0 || diskonValue > 100) {
    alert("Diskon harus antara 0-100%");
    return;
  }
  cart[index].diskon_persen = diskonValue;
  renderCart();
  updateTotal();
}

// Update item quantity
function updateQuantity(index, change) {
  const item = cart[index];
  const newQuantity = item.jumlah + change;

  if (newQuantity <= 0) {
    removeFromCart(index);
  } else if (newQuantity <= item.stok) {
    item.jumlah = newQuantity;
    renderCart();
    updateTotal();
  } else {
    alert("⚠️ Stok tidak mencukupi! Stok tersedia: " + item.stok);
  }
}

// Remove item from cart
function removeFromCart(index) {
  cart.splice(index, 1);
  renderCart();
  updateTotal();
}

// Clear entire cart
function clearCart() {
  if (cart.length > 0 && confirm("Kosongkan keranjang?")) {
    cart = [];
    renderCart();
    updateTotal();
    document.getElementById("paymentAmount").value = 0;
    document.getElementById("diskonValue").value = 0;
    document.getElementById("changeSection").classList.add("hidden");
  }
}

// Update total calculation with percentage discount only
function updateTotal() {
  // Hitung subtotal dengan diskon per item
  const subtotal = cart.reduce((sum, item) => {
    const hargaTotal = item.harga * item.jumlah;
    const diskonAmount = hargaTotal * (item.diskon_persen / 100);
    return sum + (hargaTotal - diskonAmount);
  }, 0);

  // Get transaction discount (percentage only)
  let diskonValue = parseFloat(document.getElementById("diskonValue").value) || 0;

  // Validasi persentase
  if (diskonValue < 0) diskonValue = 0;
  if (diskonValue > 100) diskonValue = 100;

  const diskonAmount = subtotal * (diskonValue / 100);
  const total = Math.max(0, subtotal - diskonAmount);

  document.getElementById("subtotal").textContent = "Rp " + subtotal.toLocaleString("id-ID");
  document.getElementById("discountDisplay").textContent = "Rp " + diskonAmount.toLocaleString("id-ID");
  document.getElementById("total").textContent = "Rp " + total.toLocaleString("id-ID");

  calculateChange();
}

// Calculate change
function calculateChange() {
  const subtotal = cart.reduce((sum, item) => {
    const hargaTotal = item.harga * item.jumlah;
    const diskonAmount = hargaTotal * (item.diskon_persen / 100);
    return sum + (hargaTotal - diskonAmount);
  }, 0);

  let diskonValue = parseFloat(document.getElementById("diskonValue").value) || 0;
  if (diskonValue < 0) diskonValue = 0;
  if (diskonValue > 100) diskonValue = 100;

  const diskonAmount = subtotal * (diskonValue / 100);
  const total = Math.max(0, subtotal - diskonAmount);
  const payment = parseFloat(document.getElementById("paymentAmount").value) || 0;
  const change = payment - total;

  const changeSection = document.getElementById("changeSection");
  const changeDisplay = document.getElementById("change");

  if (payment > 0 && payment >= total) {
    changeSection.classList.remove("hidden");
    changeDisplay.textContent = "Rp " + change.toLocaleString("id-ID");
    changeDisplay.classList.remove("text-red-600");
    changeDisplay.classList.add("text-green-600");
  } else if (payment > 0 && payment < total) {
    changeSection.classList.remove("hidden");
    changeDisplay.textContent = "Kurang Rp " + Math.abs(change).toLocaleString("id-ID");
    changeDisplay.classList.remove("text-green-600");
    changeDisplay.classList.add("text-red-600");
  } else {
    changeSection.classList.add("hidden");
  }
}

// Process payment with percentage discount only
async function processPayment() {
  if (cart.length === 0) {
    alert("⚠️ Keranjang masih kosong!");
    return;
  }

  const subtotal = cart.reduce((sum, item) => {
    const hargaTotal = item.harga * item.jumlah;
    const diskonAmount = hargaTotal * (item.diskon_persen / 100);
    return sum + (hargaTotal - diskonAmount);
  }, 0);

  let diskonValue = parseFloat(document.getElementById("diskonValue").value) || 0;
  if (diskonValue < 0) diskonValue = 0;
  if (diskonValue > 100) diskonValue = 100;

  const diskonAmount = subtotal * (diskonValue / 100);
  const total = Math.max(0, subtotal - diskonAmount);
  const payment = parseFloat(document.getElementById("paymentAmount").value);

  // Get payment method
  const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || "cash";

  // Validate payment for cash only
  if (paymentMethod === "cash") {
    if (!payment || payment < total) {
      alert("⚠️ Jumlah pembayaran tidak mencukupi!");
      return;
    }
  }

  // Prepare items data with discount
  const items = cart.map((item) => ({
    barang_id: item.id,
    jumlah: item.jumlah,
    harga: item.harga,
    diskon_persen: item.diskon_persen || 0,
  }));

  const data = {
    items: items,
    payment_method: paymentMethod,
    payment_amount: paymentMethod === "cash" ? payment : total,
    diskon_type: diskonValue > 0 ? "percentage" : "none",
    diskon_value: diskonValue,
  };

  try {
    const response = await fetch("/transaksi", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.success) {
      // === SUCCESS: All payment methods (CASH, QRIS, TRANSFER) ===
      let successMessage = `✅ Transaksi berhasil!\n\nNo Invoice: ${result.no_invoice}\nTotal: Rp ${total.toLocaleString("id-ID")}`;

      if (paymentMethod === "cash") {
        successMessage += `\nBayar: Rp ${payment.toLocaleString("id-ID")}\nKembalian: Rp ${result.change.toLocaleString("id-ID")}`;
      } else if (paymentMethod === "qris") {
        successMessage += `\nMetode: QRIS\nStatus: LUNAS ✅`;
      } else if (paymentMethod === "transfer_bca") {
        successMessage += `\nMetode: Transfer BCA\nStatus: LUNAS ✅`;
      }

      alert(successMessage);

      // Open print dialog
      if (confirm("Cetak struk sekarang?")) {
        window.open(`/transaksi/${result.transaksi_id}/print`, "_blank");
      }

      // Reset form
      cart = [];
      renderCart();
      updateTotal();
      document.getElementById("paymentAmount").value = 0;
      document.getElementById("diskonValue").value = 0;
      document.getElementById("changeSection").classList.add("hidden");

      // Reload page to update transaction list
      setTimeout(() => location.reload(), 1000);
    } else {
      alert("❌ " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("❌ Terjadi kesalahan saat memproses transaksi!");
  }
}

// Product search and filter
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchProduct");
  const filterKategori = document.getElementById("filterKategori");

  if (searchInput) {
    searchInput.addEventListener("input", filterProducts);
  }
  if (filterKategori) {
    filterKategori.addEventListener("change", filterProducts);
  }

  // Set default payment method
  selectPaymentMethod("cash");
});

function filterProducts() {
  const searchTerm = document.getElementById("searchProduct").value.toLowerCase();
  const kategoriId = document.getElementById("filterKategori").value;
  const products = document.querySelectorAll(".product-card");
  let visibleCount = 0;

  products.forEach((product) => {
    const nama = product.dataset.nama.toLowerCase();
    const kode = product.dataset.kode.toLowerCase();
    const productKategori = product.dataset.kategori;

    const matchSearch = nama.includes(searchTerm) || kode.includes(searchTerm);
    const matchKategori = !kategoriId || productKategori === kategoriId;

    if (matchSearch && matchKategori) {
      product.style.display = "block";
      visibleCount++;
    } else {
      product.style.display = "none";
    }
  });

  // Show/hide no products message
  const noProducts = document.getElementById("noProducts");
  const productsGrid = document.getElementById("productsGrid");
  if (visibleCount === 0) {
    noProducts.classList.remove("hidden");
    productsGrid.classList.add("hidden");
  } else {
    noProducts.classList.add("hidden");
    productsGrid.classList.remove("hidden");
  }
}

// Handle payment method selection
function selectPaymentMethod(method) {
  const paymentAmountSection = document.getElementById("paymentAmountSection");
  const paymentAmount = document.getElementById("paymentAmount");
  const qrisDisplay = document.getElementById("qrisDisplay");
  const transferDisplay = document.getElementById("transferDisplay");

  // Hide all payment specific displays
  qrisDisplay.classList.add("hidden");
  transferDisplay.classList.add("hidden");

  // Hide all action buttons first
  document.getElementById("btnProcessCash").classList.add("hidden");
  document.getElementById("btnConfirmQRIS").classList.add("hidden");
  document.getElementById("btnConfirmTransfer").classList.add("hidden");

  // Update subtotal to payment amount for calculation
  const subtotal = cart.reduce((sum, item) => {
    const hargaTotal = item.harga * item.jumlah;
    const diskonAmount = hargaTotal * (item.diskon_persen / 100);
    return sum + (hargaTotal - diskonAmount);
  }, 0);

  let diskonValue = parseFloat(document.getElementById("diskonValue").value) || 0;
  if (diskonValue < 0) diskonValue = 0;
  if (diskonValue > 100) diskonValue = 100;

  const diskonAmount = subtotal * (diskonValue / 100);
  const total = Math.max(0, subtotal - diskonAmount);

  if (method === "cash") {
    // Tunai: show input field for manual entry
    paymentAmountSection.style.display = "block";
    paymentAmount.disabled = false;
    paymentAmount.placeholder = "Masukkan jumlah bayar...";
    document.getElementById("btnProcessCash").classList.remove("hidden");
  } else if (method === "qris") {
    // QRIS: show QR code and hide input
    paymentAmountSection.style.display = "none";
    qrisDisplay.classList.remove("hidden");
    document.getElementById("btnConfirmQRIS").classList.remove("hidden");

    // Generate QRIS (you can replace with dynamic QR generation)
    generateQRIS(total);
  } else if (method === "transfer_bca") {
    // Transfer: show account info and hide input
    paymentAmountSection.style.display = "none";
    transferDisplay.classList.remove("hidden");
    document.getElementById("btnConfirmTransfer").classList.remove("hidden");

    // Update transfer amount display
    document.getElementById("transferAmount").textContent = "Rp " + total.toLocaleString("id-ID");
  }

  // Auto-fill payment amount for non-cash methods
  if (method !== "cash") {
    paymentAmount.value = total;
  }

  calculateChange();
}

// Generate QRIS Code (placeholder - replace with your actual QRIS image)
function generateQRIS(amount) {
  const qrisImage = document.getElementById("qrisImage");

  // Option 1: If you have a static QR code image
  qrisImage.src = "/images/qris-code.png"; // Path to your QRIS image

  // Option 2: If you want to generate dynamic QR (requires QR library)
  // You can use a library like qrcode.js or generate from backend
  // Example: qrisImage.src = "data:image/png;base64," + generateQRCodeBase64(amount);

  // Option 3: Generate via API endpoint
  // qrisImage.src = `/api/generate-qris?amount=${amount}`;
}

// Copy account number to clipboard
function copyAccountNumber() {
  const accountNumber = document.getElementById("accountNumber").textContent;

  navigator.clipboard
    .writeText(accountNumber)
    .then(() => {
      // Show success message
      const btn = event.target.closest("button");
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-check"></i>';
      btn.classList.add("text-green-600");

      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove("text-green-600");
      }, 2000);

      // Optional: show toast notification
      alert("✅ Nomor rekening berhasil disalin!");
    })
    .catch((err) => {
      console.error("Failed to copy:", err);
      alert("❌ Gagal menyalin nomor rekening");
    });
}

// Confirm digital payment (QRIS or Transfer)
// ⚡ OPTIMIZED: Auto-confirm setelah kasir verifikasi pembayaran customer
function confirmDigitalPayment(method) {
  if (cart.length === 0) {
    alert("⚠️ Keranjang masih kosong!");
    return;
  }

  const methodName = method === "qris" ? "QRIS" : "Transfer BCA";

  // Show confirmation dialog - Kasir wajib verifikasi pembayaran customer dulu
  if (!confirm(`⚠️ Apakah pembayaran via ${methodName} sudah diterima?\n\nPastikan Anda telah menerima konfirmasi pembayaran sebelum melanjutkan!`)) {
    return;
  }

  // ⚡ AUTO-CONFIRM: Langsung proses tanpa delay
  // Process payment immediately after confirmation
  processPayment();
}
