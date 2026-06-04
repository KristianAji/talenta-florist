// js/main.js – Digunakan di semua halaman publik

// ── PAGE-OUT TRANSITION ──
document.querySelectorAll('a[href]').forEach(a => {
  const href   = a.getAttribute('href');
  const target = a.getAttribute('target');

  // Lewati link yang:
  // - kosong atau tidak ada
  // - buka tab baru (_blank)
  // - eksternal (http/https)
  // - anchor (#)
  // - protokol khusus (mailto:, tel:, javascript:)
  if (
    !href ||
    href === '' ||
    target === '_blank' ||
    href.startsWith('http') ||
    href.startsWith('#') ||
    href.startsWith('mailto') ||
    href.startsWith('tel') ||
    href.startsWith('javascript')
  ) return;

  a.addEventListener('click', e => {
    e.preventDefault();
    document.body.style.animation = 'pageOut .32s ease forwards';
    setTimeout(() => { location.href = href; }, 310);
  });
});

// ── SCROLL REVEAL ──
const revealObs = new IntersectionObserver(entries => {
  entries.forEach((en, i) => {
    if (en.isIntersecting) {
      setTimeout(() => en.target.classList.add('visible'), i * 90);
      revealObs.unobserve(en.target);
    }
  });
}, { threshold: .12 });

document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => revealObs.observe(el));

// ── RESET ANIMASI SAAT BACK/FORWARD ──
window.addEventListener('pageshow', (e) => {
  if (e.persisted) {
    document.body.style.animation = '';
    document.body.style.opacity   = '1';
  }
});

// ── TOGGLE ULASAN PUBLIK (testimoni.php) ──
function toggleUlasan() {
  const grid = document.getElementById('publik-grid');
  const btn  = document.getElementById('toggleBtn');
  if (!grid || !btn) return;

  const isHidden = grid.style.display === 'none';

  if (isHidden) {
    grid.style.display = 'grid';
    btn.classList.add('active');
    btn.textContent = '✖ Sembunyikan Ulasan';
    // Trigger animasi ulang
    grid.querySelectorAll('.publik-card').forEach((c, i) => {
      c.style.animation = 'none';
      c.offsetHeight; // reflow
      c.style.animation = '';
      c.style.animationDelay = (i * 0.07) + 's';
    });
    grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } else {
    grid.style.display = 'none';
    btn.classList.remove('active');
    const total = grid.querySelectorAll('.publik-card').length;
    btn.textContent = '🌸 Lihat Ulasan (' + total + ')';
  }
}