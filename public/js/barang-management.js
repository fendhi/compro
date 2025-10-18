// Barang Management JavaScript

// Modal Functions
function openAddModal() {
  document.getElementById("modalTitle").textContent = "Tambah Barang";
  document.getElementById("submitButton").textContent = "Simpan";
  document.getElementById("barangForm").action = "/master-data/barang";
  document.getElementById("methodField").value = "";
  document.getElementById("barangForm").reset();
  document.getElementById("barangModal").classList.remove("hidden");
}

function openEditModal(barang) {
  document.getElementById("modalTitle").textContent = "Edit Barang";
  document.getElementById("submitButton").textContent = "Update";
  document.getElementById("barangForm").action = "/master-data/barang/" + barang.id;
  document.getElementById("methodField").value = "PUT";
  document.getElementById("nama").value = barang.nama;
  document.getElementById("kategori_id").value = barang.kategori_id;
  document.getElementById("jenis_barang").value = barang.jenis_barang;
  document.getElementById("harga_modal").value = barang.harga_modal || 0;
  document.getElementById("harga").value = barang.harga;
  document.getElementById("stok").value = barang.stok;
  document.getElementById("satuan").value = "pcs"; // Always PCS
  document.getElementById("barangModal").classList.remove("hidden");

  // Trigger profit calculation
  const event = new Event("input");
  document.getElementById("harga").dispatchEvent(event);
}

function closeModal() {
  document.getElementById("barangModal").classList.add("hidden");
  document.getElementById("barangForm").reset();
}

// Filter Table Function
function filterTable() {
  const searchValue = document.getElementById("searchBarang").value.toLowerCase();
  const kategoriValue = document.getElementById("filterKategori").value;
  const stokValue = document.getElementById("filterStok").value;
  const rows = document.querySelectorAll("#tableBody tr[data-nama]");
  let visibleCount = 0;

  rows.forEach((row) => {
    const nama = row.dataset.nama;
    const kode = row.dataset.kode;
    const kategori = row.dataset.kategori;
    const stok = parseInt(row.dataset.stok);

    let matchSearch = nama.includes(searchValue) || kode.includes(searchValue);
    let matchKategori = !kategoriValue || kategori === kategoriValue;
    let matchStok = true;

    if (stokValue === "low") matchStok = stok < 10;
    else if (stokValue === "medium") matchStok = stok >= 10 && stok <= 50;
    else if (stokValue === "high") matchStok = stok > 50;

    if (matchSearch && matchKategori && matchStok) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
    }
  });

  const noResults = document.getElementById("noResults");
  const tableBody = document.getElementById("tableBody");

  if (visibleCount === 0 && rows.length > 0) {
    noResults.classList.remove("hidden");
    tableBody.classList.add("hidden");
  } else {
    noResults.classList.add("hidden");
    tableBody.classList.remove("hidden");
  }
}

// Event Listeners
document.addEventListener("DOMContentLoaded", function () {
  // Auto-open modal if there are validation errors
  const hasErrors = document.querySelector(".bg-red-50");
  if (hasErrors) {
    // Check if there's old input data (means form was submitted)
    const namaInput = document.getElementById("nama");
    if (namaInput && namaInput.value) {
      document.getElementById("barangModal").classList.remove("hidden");
    }
  }

  // Close modal on ESC
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeModal();
    }
  });

  // Close modal on outside click
  const modal = document.getElementById("barangModal");
  if (modal) {
    modal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeModal();
      }
    });
  }

  // Real-time profit calculation
  const hargaModalInput = document.getElementById("harga_modal");
  const hargaJualInput = document.getElementById("harga");
  const profitInfo = document.getElementById("profitInfo");

  function calculateProfit() {
    const hargaModal = parseFloat(hargaModalInput.value) || 0;
    const hargaJual = parseFloat(hargaJualInput.value) || 0;

    if (hargaModal > 0 && hargaJual > 0) {
      const profit = hargaJual - hargaModal;
      const margin = ((profit / hargaModal) * 100).toFixed(1);

      if (profit > 0) {
        profitInfo.innerHTML = `<span class="text-green-600"><i class="fas fa-arrow-up"></i> Profit: Rp ${profit.toLocaleString("id-ID")} (${margin}%)</span>`;
      } else if (profit < 0) {
        profitInfo.innerHTML = `<span class="text-red-600"><i class="fas fa-arrow-down"></i> Rugi: Rp ${Math.abs(profit).toLocaleString("id-ID")} (${margin}%)</span>`;
      } else {
        profitInfo.innerHTML = `<span class="text-gray-600">Impas (0% profit)</span>`;
      }
    } else {
      profitInfo.innerHTML = "";
    }
  }

  if (hargaModalInput && hargaJualInput) {
    hargaModalInput.addEventListener("input", calculateProfit);
    hargaJualInput.addEventListener("input", calculateProfit);
  }
});
