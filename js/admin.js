// js/admin.js – Sidebar toggle, modal, preview gambar

// ── SIDEBAR TOGGLE (desktop & mobile) ──
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar       = document.getElementById('admin-sidebar');
const adminMain     = document.querySelector('.admin-main');

const isMobile = () => window.innerWidth <= 768;

function setSidebar(open) {
  if (isMobile()) {
    // Mobile: pakai class open/tutup
    if (open) {
      sidebar.classList.add('open');
    } else {
      sidebar.classList.remove('open');
    }
  } else {
    // Desktop: pakai class collapsed
    if (open) {
      sidebar.classList.remove('collapsed');
      adminMain && adminMain.classList.remove('expanded');
      localStorage.setItem('sidebar_state', 'open');
    } else {
      sidebar.classList.add('collapsed');
      adminMain && adminMain.classList.add('expanded');
      localStorage.setItem('sidebar_state', 'closed');
    }
  }
}

if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => {
    if (isMobile()) {
      setSidebar(!sidebar.classList.contains('open'));
    } else {
      setSidebar(sidebar.classList.contains('collapsed'));
    }
  });
}

// Tutup sidebar mobile kalau klik di luar
document.addEventListener('click', e => {
  if (isMobile() &&
      sidebar.classList.contains('open') &&
      !sidebar.contains(e.target) &&
      e.target !== sidebarToggle) {
    setSidebar(false);
  }
});

// Reset saat resize dari mobile ke desktop
window.addEventListener('resize', () => {
  if (!isMobile()) sidebar.classList.remove('open');
});

// Restore state desktop saat halaman dibuka
if (!isMobile()) {
  const savedState = localStorage.getItem('sidebar_state');
  if (savedState === 'closed') setSidebar(false);
}

// Tombol close di dalam sidebar (mobile)
const sidebarClose = document.getElementById('sidebar-close');
if (sidebarClose) {
  sidebarClose.addEventListener('click', () => setSidebar(false));
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
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});

// ── FLASH MESSAGE AUTO-HIDE ──
const flash = document.querySelector('.flash-msg');
if (flash) setTimeout(() => {
    flash.style.transition = 'opacity .4s';
    flash.style.opacity = '0';
    setTimeout(() => flash.remove(), 400);
}, 3500);