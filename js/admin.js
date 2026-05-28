// js/admin.js – Sidebar toggle, modal, preview gambar

// ── SIDEBAR TOGGLE (mobile) ──
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar       = document.getElementById('admin-sidebar');
if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  document.addEventListener('click', e => {
    if (!sidebar.contains(e.target) && e.target !== sidebarToggle) sidebar.classList.remove('open');
  });
}

// ── KONFIRMASI HAPUS ──
document.querySelectorAll('.btn-hapus').forEach(btn => {
  btn.addEventListener('click', e => {
    if (!confirm('Yakin ingin menghapus data ini?')) e.preventDefault();
  });
});

// ── PREVIEW GAMBAR UPLOAD ──
document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
  input.addEventListener('change', () => {
    const preview = document.getElementById(input.dataset.preview);
    if (!preview) return;
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
      reader.readAsDataURL(file);
    }
  });
});

// ── FLASH MESSAGE AUTO-HIDE ──
const flash = document.querySelector('.flash-msg');
if (flash) setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 400); }, 3500);