// ============================================================
// js/notif-dropdown.js
// Dropdown notifikasi admin — include di halaman admin
// ============================================================

(function () {
  const bell     = document.getElementById('notif-bell');
  const dropdown = document.getElementById('notif-dropdown');
  const badge    = document.getElementById('notif-badge');
  const ddList   = document.getElementById('notif-dd-list');
  const ddCount  = document.getElementById('notif-dd-count');

  if (!bell || !dropdown) return;

  let loaded = false;

  // ── IKON BERDASARKAN PESAN ──
  function getIcon(pesan) {
    if (pesan.includes('baru'))        return '🌸';
    if (pesan.includes('dibatalkan'))  return '❌';
    if (pesan.includes('ulasan'))      return '⭐';
    return '📋';
  }

  // ── RENDER ITEM ──
  function renderItems(notifs) {
    if (!notifs.length) {
      ddList.innerHTML = `
        <div class="notif-dd-empty">
          <span>🌸</span>
          Belum ada notifikasi masuk
        </div>`;
      return;
    }

    ddList.innerHTML = notifs.map(n => `
      <a class="notif-dd-item ${n.dibaca ? '' : 'belum-dibaca'}"
         href="${n.link ? (window.BASE_URL || '/') + n.link : '#'}"
        <div class="notif-dd-icon">${getIcon(n.pesan)}</div>
        <div class="notif-dd-body">
          <p class="notif-dd-pesan">${escHtml(n.pesan)}</p>
          ${n.nama_pelanggan
            ? `<span class="notif-dd-dari">dari ${escHtml(n.nama_pelanggan)}</span>`
            : ''}
          <span class="notif-dd-waktu">${escHtml(n.waktu)}</span>
        </div>
        ${n.dibaca ? '' : '<div class="notif-dd-dot"></div>'}
      </a>
    `).join('');
  }

  // ── ESCAPE HTML ──
  function escHtml(str) {
    return String(str)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ── FETCH DATA ──
  function fetchDropdown() {
    fetch((window.BASE_URL || '') + 'api/notif_dropdown.php', { credentials: 'same-origin' })
      .then(r => r.ok ? r.json() : null)
      .then(data => {
        if (!data?.ok) return;
        loaded = true;

        // Update badge
        const count = data.count || 0;
        badge.textContent   = count > 99 ? '99+' : count;
        badge.style.display = count > 0 ? 'flex' : 'none';
        bell.classList.toggle('has-notif', count > 0);

        // Update count label
        if (ddCount) ddCount.textContent = count > 0 ? `${count} baru` : 'Terbaru';

        // Update judul tab
        const baseTitle = document.title.replace(/^\(\d+\+?\) /, '');
        document.title  = count > 0 ? `(${count}) ${baseTitle}` : baseTitle;

        renderItems(data.notifs);
      })
      .catch(() => {});
  }

  // ── TOGGLE DROPDOWN ──
  bell.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = dropdown.classList.toggle('open');
    if (isOpen && !loaded) fetchDropdown();
  });

  // ── TUTUP SAAT KLIK DI LUAR ──
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && e.target !== bell) {
      dropdown.classList.remove('open');
    }
  });

  // ── POLLING TIAP 30 DETIK ──
  fetchDropdown();
  setInterval(fetchDropdown, 30_000);
})();