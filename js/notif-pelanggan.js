(function () {
  const badge = document.getElementById('notif-badge');
  if (!badge) return;

  function fetchNotif() {
    const base = window.BASE_URL || '/';
    fetch(base + 'api/notif_pelanggan.php', { credentials: 'same-origin' })
      .then(r => r.ok ? r.json() : null)
      .then(data => {
        if (!data?.ok) return;
        const count = data.count || 0;
        badge.textContent   = count > 99 ? '99+' : count;
        badge.style.display = count > 0 ? 'flex' : 'none';

        // Update judul tab
        const baseTitle = document.title.replace(/^\(\d+\+?\) /, '');
        document.title = count > 0 ? `(${count}) ${baseTitle}` : baseTitle;
      })
      .catch(() => {});
  }

  fetchNotif();
  setInterval(fetchNotif, 30_000);
})();