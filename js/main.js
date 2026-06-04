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

// ── TOGGLE PASSWORD (LOGIN) ──
function togglePassword() {
  const input = document.getElementById('password');
  const btn   = document.getElementById('eyeBtn');
  if (!input || !btn) return;

  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';

  // Ganti ikon: mata terbuka vs mata dengan garis coret
  btn.innerHTML = isHidden
    ? `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`
    : `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
}
