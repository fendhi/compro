/**
 * OrindPOS - Universal Notification System
 * Standardized SweetAlert2 untuk semua modul CRUD
 * Version: 1.0.0
 */

// ===== NOTIFICATION HELPERS =====
const OrindNotification = {
  /**
   * SUCCESS Notification (Auto-close dengan progress bar)
   */
  success: function (message, redirectUrl = null, timer = 1500) {
    Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: message,
      showConfirmButton: false,
      timer: timer,
      timerProgressBar: true,
    }).then(() => {
      if (redirectUrl) {
        window.location.href = redirectUrl;
      }
    });
  },

  /**
   * ERROR Notification
   */
  error: function (message, title = "Gagal!") {
    Swal.fire({
      icon: "error",
      title: title,
      html: message,
      confirmButtonColor: "#00718F",
      confirmButtonText: "OK",
    });
  },

  /**
   * WARNING/CONFIRM Delete Dialog
   */
  confirmDelete: function (entityName, callback) {
    Swal.fire({
      title: `Hapus ${entityName}?`,
      text: "Data yang sudah dihapus tidak dapat dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#EF4444",
      cancelButtonColor: "#6B7280",
      confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, Hapus!',
      cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading
        Swal.fire({
          title: "Menghapus...",
          text: "Mohon tunggu sebentar",
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        // Execute callback
        callback();
      }
    });
  },

  /**
   * LOADING State
   */
  loading: function (message = "Memproses...") {
    Swal.fire({
      title: message,
      text: "Mohon tunggu sebentar",
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
  },

  /**
   * Close any open Swal
   */
  close: function () {
    Swal.close();
  },
};

// ===== GLOBAL DELETE FUNCTION untuk form dengan confirm =====
window.confirmDeleteForm = function (event, entityName) {
  event.preventDefault();
  const form = event.target;

  OrindNotification.confirmDelete(entityName, function () {
    // Submit form after confirmation
    form.submit();
  });

  return false;
};

// ===== AJAX DELETE FUNCTION untuk delete via AJAX =====
window.deleteWithAjax = function (url, entityName, redirectUrl = null) {
  OrindNotification.confirmDelete(entityName, function () {
    $.ajax({
      url: url,
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        OrindNotification.success(`${entityName} berhasil dihapus!`, redirectUrl);

        // Reload if no redirect URL
        if (!redirectUrl) {
          setTimeout(() => location.reload(), 1500);
        }
      },
      error: function (xhr) {
        OrindNotification.error(xhr.responseJSON?.message || `Gagal menghapus ${entityName.toLowerCase()}`);
      },
    });
  });
};

// ===== FORM SUBMIT dengan AJAX (CREATE & UPDATE) =====
window.submitFormAjax = function (formId, entityName, action = "create") {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Get submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Disable and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = action === "create" ? '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...' : '<i class="fas fa-spinner fa-spin mr-2"></i>Mengupdate...';

    // Prepare form data
    const formData = new FormData(form);

    $.ajax({
      url: form.action,
      method: form.method || "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        const message = action === "create" ? `${entityName} berhasil ditambahkan!` : `${entityName} berhasil diperbarui!`;

        OrindNotification.success(message, response.redirect || null);

        // Reload if no redirect
        if (!response.redirect) {
          setTimeout(() => location.reload(), 1500);
        }
      },
      error: function (xhr) {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

        // Parse errors
        let errorMessage = "Terjadi kesalahan saat menyimpan data";

        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = xhr.responseJSON.errors;
          errorMessage = Object.values(errors).flat().join("<br>");
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }

        OrindNotification.error(errorMessage);
      },
    });
  });
};

// ===== EXPORT untuk digunakan di modul lain =====
window.OrindNotification = OrindNotification;
