// Laporan Bulanan JavaScript

// Export to Excel
function exportExcel() {
  const bulan = document.getElementById("bulan").value;
  const kasir = document.getElementById("kasir").value;

  const params = new URLSearchParams();
  params.append("bulan", bulan);
  if (kasir) params.append("kasir", kasir);
  params.append("export", "excel");

  window.location.href = `/laporan/bulanan?${params.toString()}`;
}

// Export to PDF
function exportPDF() {
  const bulan = document.getElementById("bulan").value;
  const kasir = document.getElementById("kasir").value;

  const params = new URLSearchParams();
  params.append("bulan", bulan);
  if (kasir) params.append("kasir", kasir);
  params.append("export", "pdf");

  window.location.href = `/laporan/bulanan?${params.toString()}`;
}

// Auto submit filter on change
document.getElementById("bulan")?.addEventListener("change", function () {
  document.getElementById("filterForm").submit();
});

document.getElementById("kasir")?.addEventListener("change", function () {
  document.getElementById("filterForm").submit();
});
