// Laporan Harian JavaScript

// Export to Excel
function exportExcel() {
  const tanggal = document.getElementById("tanggal").value;
  const kasir = document.getElementById("kasir").value;

  const params = new URLSearchParams();
  params.append("tanggal", tanggal);
  if (kasir) params.append("kasir", kasir);
  params.append("export", "excel");

  window.location.href = `/laporan/harian?${params.toString()}`;
}

// Export to PDF
function exportPDF() {
  const tanggal = document.getElementById("tanggal").value;
  const kasir = document.getElementById("kasir").value;

  const params = new URLSearchParams();
  params.append("tanggal", tanggal);
  if (kasir) params.append("kasir", kasir);
  params.append("export", "pdf");

  window.location.href = `/laporan/harian?${params.toString()}`;
}

// Auto submit filter on change
document.getElementById("tanggal")?.addEventListener("change", function () {
  document.getElementById("filterForm").submit();
});

document.getElementById("kasir")?.addEventListener("change", function () {
  document.getElementById("filterForm").submit();
});
