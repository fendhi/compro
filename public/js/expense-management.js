/**
 * Expense Management JavaScript
 * OrindPOS Vapor - Financial Management
 */

$(document).ready(function () {
  // ===== CURRENCY FORMATTING =====
  function formatRupiah(angka) {
    if (!angka) return "";
    const number = angka.toString().replace(/[^0-9]/g, "");
    return number.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  function unformatRupiah(angka) {
    return angka.toString().replace(/[^0-9]/g, "");
  }

  // Format nominal input
  $("#nominalInput").on("keyup", function () {
    let value = $(this).val();
    let unformatted = unformatRupiah(value);
    $(this).val(formatRupiah(unformatted));
  });

  // ===== IMAGE UPLOAD PREVIEW =====
  $("#buktiInput").on("change", function (e) {
    const file = e.target.files[0];

    if (file) {
      // Validate file size (max 2MB)
      if (file.size > 2048000) {
        alert("Ukuran file terlalu besar! Maksimal 2MB");
        $(this).val("");
        return;
      }

      // Validate file type
      if (!file.type.match("image.*")) {
        alert("File harus berupa gambar!");
        $(this).val("");
        return;
      }

      // Show preview
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#previewImg").attr("src", e.target.result);
        $("#imagePreview").removeClass("hidden");
        $("#uploadLabel").hide();
      };
      reader.readAsDataURL(file);
    }
  });

  // ===== FORM SUBMIT (CREATE) =====
  $("#formPengeluaran").on("submit", function (e) {
    e.preventDefault();

    // Disable submit button
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');

    // Prepare form data
    const formData = new FormData(this);

    // Replace formatted nominal with raw value
    const nominalFormatted = $("#nominalInput").val();
    const nominalRaw = unformatRupiah(nominalFormatted);
    formData.set("nominal", nominalRaw);

    $.ajax({
      url: $(this).attr("action") || "/keuangan/pengeluaran",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: response.message || "Pengeluaran berhasil ditambahkan",
          showConfirmButton: false,
          timer: 1500,
        });

        setTimeout(function () {
          window.location.href = "/keuangan/pengeluaran";
        }, 1500);
      },
      error: function (xhr) {
        submitBtn.prop("disabled", false).html('<i class="fas fa-save mr-2"></i>Simpan Pengeluaran');

        let errorMessage = "Terjadi kesalahan saat menyimpan data";

        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = xhr.responseJSON.errors;
          errorMessage = Object.values(errors).flat().join("<br>");
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }

        Swal.fire({
          icon: "error",
          title: "Error!",
          html: errorMessage,
        });
      },
    });
  });

  // ===== EDIT EXPENSE =====
  window.editExpense = function (id) {
    window.location.href = `/keuangan/pengeluaran/${id}/edit`;
  };

  // ===== DELETE EXPENSE =====
  window.deleteExpense = function (id) {
    Swal.fire({
      title: "Hapus Pengeluaran?",
      text: "Data yang sudah dihapus tidak dapat dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#EF4444",
      cancelButtonColor: "#6B7280",
      confirmButtonText: "Ya, Hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/keuangan/pengeluaran/${id}`,
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
          success: function (response) {
            Swal.fire({
              icon: "success",
              title: "Terhapus!",
              text: response.message || "Pengeluaran berhasil dihapus",
              showConfirmButton: false,
              timer: 1500,
            });

            setTimeout(function () {
              location.reload();
            }, 1500);
          },
          error: function (xhr) {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: xhr.responseJSON?.message || "Gagal menghapus data",
            });
          },
        });
      }
    });
  };

  // ===== FORM VALIDATION =====
  function validateForm() {
    let isValid = true;
    const errors = [];

    // Validate tanggal
    if (!$('input[name="tanggal"]').val()) {
      errors.push("Tanggal harus diisi");
      isValid = false;
    }

    // Validate kategori
    if (!$("#subKategori").val()) {
      errors.push("Kategori harus dipilih");
      isValid = false;
    }

    // Validate deskripsi
    if (!$('input[name="deskripsi"]').val().trim()) {
      errors.push("Deskripsi harus diisi");
      isValid = false;
    }

    // Validate nominal
    const nominal = unformatRupiah($("#nominalInput").val());
    if (!nominal || parseInt(nominal) <= 0) {
      errors.push("Nominal harus lebih dari 0");
      isValid = false;
    }

    // Show errors if any
    if (!isValid) {
      Swal.fire({
        icon: "warning",
        title: "Validasi Gagal",
        html: errors.join("<br>"),
      });
    }

    return isValid;
  }

  // ===== FILTER FORM AUTO-SUBMIT =====
  $(".filter-auto-submit").on("change", function () {
    $(this).closest("form").submit();
  });
});

// ===== REMOVE IMAGE FUNCTION (Global) =====
function removeImage() {
  $("#buktiInput").val("");
  $("#imagePreview").addClass("hidden");
  $("#uploadLabel").show();
  $("#previewImg").attr("src", "");
}
