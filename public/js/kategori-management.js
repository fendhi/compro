// Kategori Management JavaScript
const modal = document.getElementById("kategoriModal");
const form = document.getElementById("kategoriForm");

// Open Add Modal
function openAddModal() {
  document.getElementById("modalTitle").textContent = "Tambah Kategori";
  document.getElementById("submitButton").textContent = "Simpan";
  document.getElementById("methodField").value = "";
  form.action = "/master-data/kategori";
  form.reset();
  modal.classList.remove("hidden");
  document.getElementById("nama").focus();
}

// Open Edit Modal
function openEditModal(kategori) {
  document.getElementById("modalTitle").textContent = "Edit Kategori";
  document.getElementById("submitButton").textContent = "Update";
  document.getElementById("methodField").value = "PUT";
  form.action = `/master-data/kategori/${kategori.id}`;

  document.getElementById("nama").value = kategori.nama;
  document.getElementById("deskripsi").value = kategori.deskripsi || "";

  modal.classList.remove("hidden");
  document.getElementById("nama").focus();
}

// Close Modal
function closeModal() {
  modal.classList.add("hidden");
  form.reset();
}

// Filter Cards
function filterCards() {
  const search = document.getElementById("searchKategori").value.toLowerCase();
  const cards = document.querySelectorAll(".category-card");
  const noResults = document.getElementById("noResults");
  let visibleCount = 0;

  cards.forEach((card) => {
    const nama = card.dataset.nama;
    const deskripsi = card.dataset.deskripsi;

    if (nama.includes(search) || deskripsi.includes(search)) {
      card.style.display = "block";
      visibleCount++;
    } else {
      card.style.display = "none";
    }
  });

  // Show/hide no results message
  if (visibleCount === 0 && cards.length > 0) {
    noResults.classList.remove("hidden");
  } else {
    noResults.classList.add("hidden");
  }
}

// Event Listeners
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeModal();
});

modal.addEventListener("click", (e) => {
  if (e.target === modal) closeModal();
});

// Auto-hide success notification after 3 seconds
document.addEventListener("DOMContentLoaded", () => {
  const successAlert = document.querySelector(".alert-success");
  if (successAlert) {
    setTimeout(() => {
      successAlert.style.transition = "opacity 0.5s ease";
      successAlert.style.opacity = "0";
      setTimeout(() => successAlert.remove(), 500);
    }, 3000);
  }
});
