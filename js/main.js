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