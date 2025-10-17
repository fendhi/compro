// User Management JavaScript
const modal = document.getElementById("userModal");
const form = document.getElementById("userForm");

// Open Add Modal
function openAddModal() {
  document.getElementById("modalTitle").textContent = "Tambah User";
  document.getElementById("submitButton").textContent = "Simpan";
  document.getElementById("methodField").value = "";
  document.getElementById("passwordHint").textContent = "Min. 6 karakter";

  // Make password required for add
  document.getElementById("password").required = true;
  document.getElementById("password_confirmation").required = true;

  form.action = "/management/user";
  form.reset();

  // Set default values
  document.getElementById("role").value = "kasir";
  document.getElementById("status").value = "active";

  modal.classList.remove("hidden");
  document.getElementById("name").focus();
}

// Open Edit Modal
function openEditModal(user) {
  document.getElementById("modalTitle").textContent = "Edit User";
  document.getElementById("submitButton").textContent = "Update";
  document.getElementById("methodField").value = "PUT";
  document.getElementById("passwordHint").textContent = "Kosongkan jika tidak ingin mengubah password";

  // Make password optional for edit
  document.getElementById("password").required = false;
  document.getElementById("password_confirmation").required = false;

  form.action = `/management/user/${user.id}`;

  // Fill form
  document.getElementById("name").value = user.name;
  document.getElementById("username").value = user.username;
  document.getElementById("email").value = user.email;
  document.getElementById("role").value = user.role;
  document.getElementById("status").value = user.status || "active";
  document.getElementById("password").value = "";
  document.getElementById("password_confirmation").value = "";

  modal.classList.remove("hidden");
  document.getElementById("name").focus();
}

// Close Modal
function closeModal() {
  modal.classList.add("hidden");
  form.reset();
}

// Filter Table
function filterTable() {
  const search = document.getElementById("searchUser").value.toLowerCase();
  const role = document.getElementById("filterRole").value.toLowerCase();
  const rows = document.querySelectorAll("#userTable tbody tr");

  rows.forEach((row) => {
    const nama = row.dataset.nama || "";
    const username = row.dataset.username || "";
    const email = row.dataset.email || "";
    const userRole = row.dataset.role || "";

    const matchSearch = nama.includes(search) || username.includes(search) || email.includes(search);
    const matchRole = !role || userRole === role;

    if (matchSearch && matchRole) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Event Listeners
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeModal();
});

modal.addEventListener("click", (e) => {
  if (e.target === modal) closeModal();
});

// Password validation
document.getElementById("password_confirmation")?.addEventListener("input", function () {
  const password = document.getElementById("password").value;
  const confirmation = this.value;

  if (password && confirmation && password !== confirmation) {
    this.setCustomValidity("Password tidak cocok");
  } else {
    this.setCustomValidity("");
  }
});

// Success message auto hide
setTimeout(() => {
  const alert = document.querySelector(".alert-success, .bg-green-500");
  if (alert) {
    alert.style.transition = "opacity 0.5s";
    alert.style.opacity = "0";
    setTimeout(() => alert.remove(), 500);
  }
}, 3000);
