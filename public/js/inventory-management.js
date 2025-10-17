// inventory-management.js
// Handles inventory management operations

// Filter table
function filterTable() {
  const searchValue = document.getElementById("searchBarang").value.toLowerCase();
  const filterStok = document.getElementById("filterStok").value;
  const rows = document.querySelectorAll("#tableBody tr");

  rows.forEach((row) => {
    const nama = row.getAttribute("data-nama");
    const kode = row.getAttribute("data-kode");
    const stok = parseInt(row.getAttribute("data-stok"));
    const minimum = parseInt(row.getAttribute("data-minimum"));

    // Search filter
    const matchesSearch = nama?.includes(searchValue) || kode?.includes(searchValue);

    // Stock filter
    let matchesStok = true;
    if (filterStok === "low") {
      matchesStok = stok <= minimum;
    } else if (filterStok === "normal") {
      matchesStok = stok > minimum;
    }

    // Show/hide row
    if (matchesSearch && matchesStok) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Open History Page
function openHistoryPage() {
  window.location.href = "/inventory/history";
}

// ===== STOCK IN MODAL =====
function openStockInModal(barangId = null) {
  const modal = document.getElementById("stockInModal");
  const form = document.getElementById("stockInForm");
  form.reset();

  if (barangId) {
    document.getElementById("stockInBarangId").value = barangId;
  }

  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeStockInModal() {
  const modal = document.getElementById("stockInModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

document.getElementById("stockInForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const data = Object.fromEntries(formData);

  try {
    const response = await fetch("/inventory/stock-in", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.success) {
      alert("Stok berhasil ditambahkan!\nStok Sebelum: " + result.data.stok_before + "\nStok Setelah: " + result.data.stok_after);
      closeStockInModal();
      location.reload();
    } else {
      alert("Gagal: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Terjadi kesalahan saat menambah stok");
  }
});

// ===== STOCK OUT MODAL =====
function openStockOutModal(barangId = null) {
  const modal = document.getElementById("stockOutModal");
  const form = document.getElementById("stockOutForm");
  form.reset();

  if (barangId) {
    const select = document.getElementById("stockOutBarangId");
    select.value = barangId;
    const option = select.options[select.selectedIndex];
    const stok = option.getAttribute("data-stok");
    document.getElementById("stokTersedia").textContent = stok;
  }

  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeStockOutModal() {
  const modal = document.getElementById("stockOutModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

// Update stok tersedia saat barang dipilih
document.getElementById("stockOutBarangId").addEventListener("change", function () {
  const option = this.options[this.selectedIndex];
  const stok = option.getAttribute("data-stok");
  document.getElementById("stokTersedia").textContent = stok || "0";
});

document.getElementById("stockOutForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const data = Object.fromEntries(formData);

  try {
    const response = await fetch("/inventory/stock-out", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.success) {
      alert("Stok berhasil dikurangi!\nStok Sebelum: " + result.data.stok_before + "\nStok Setelah: " + result.data.stok_after);
      closeStockOutModal();
      location.reload();
    } else {
      alert("Gagal: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Terjadi kesalahan saat mengurangi stok");
  }
});

// ===== STOCK OPNAME MODAL =====
function openStockOpnameModal(barangId = null) {
  const modal = document.getElementById("stockOpnameModal");
  const form = document.getElementById("stockOpnameForm");
  form.reset();

  if (barangId) {
    const select = document.getElementById("stockOpnameBarangId");
    select.value = barangId;
    const option = select.options[select.selectedIndex];
    const stok = option.getAttribute("data-stok");
    document.getElementById("stokSistem").textContent = stok;
  }

  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeStockOpnameModal() {
  const modal = document.getElementById("stockOpnameModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

// Update stok sistem saat barang dipilih
document.getElementById("stockOpnameBarangId").addEventListener("change", function () {
  const option = this.options[this.selectedIndex];
  const stok = option.getAttribute("data-stok");
  document.getElementById("stokSistem").textContent = stok || "0";
});

document.getElementById("stockOpnameForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const data = Object.fromEntries(formData);

  try {
    const response = await fetch("/inventory/stock-opname", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.success) {
      const selisih = result.data.selisih;
      const selisihText = selisih >= 0 ? "+" + selisih : selisih;
      alert("Stock opname berhasil!\nStok Sebelum: " + result.data.stok_before + "\nStok Setelah: " + result.data.stok_after + "\nSelisih: " + selisihText);
      closeStockOpnameModal();
      location.reload();
    } else {
      alert("Gagal: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Terjadi kesalahan saat melakukan stock opname");
  }
});

// ===== ADJUSTMENT MODAL =====
function openAdjustmentModal(barangId = null) {
  const modal = document.getElementById("adjustmentModal");
  const form = document.getElementById("adjustmentForm");
  form.reset();

  if (barangId) {
    document.getElementById("adjustmentBarangId").value = barangId;
  }

  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeAdjustmentModal() {
  const modal = document.getElementById("adjustmentModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

document.getElementById("adjustmentForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const data = Object.fromEntries(formData);

  try {
    const response = await fetch("/inventory/adjustment", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.success) {
      alert("Penyesuaian stok berhasil!\nStok Sebelum: " + result.data.stok_before + "\nStok Setelah: " + result.data.stok_after);
      closeAdjustmentModal();
      location.reload();
    } else {
      alert("Gagal: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Terjadi kesalahan saat melakukan penyesuaian");
  }
});

// Close modals on outside click
window.onclick = function (event) {
  const modals = ["stockInModal", "stockOutModal", "stockOpnameModal", "adjustmentModal"];
  modals.forEach((modalId) => {
    const modal = document.getElementById(modalId);
    if (event.target === modal) {
      modal.classList.add("hidden");
      modal.classList.remove("flex");
    }
  });
};
