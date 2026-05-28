// js/pesan.js – Animasi langkah pemesanan & FAQ accordion

// ── STEPS ENTRANCE ──
const steps   = document.querySelectorAll('.step');
const stepObs = new IntersectionObserver(entries => {
  if (entries[0].isIntersecting) {
    steps.forEach((s, i) => setTimeout(() => s.classList.add('show'), 300 + i * 180));
    stepObs.disconnect();
  }
}, { threshold: .2 });

if (steps.length) stepObs.observe(document.querySelector('.steps-grid'));

// ── FAQ ACCORDION ──
document.querySelectorAll('.faq-q').forEach(btn => {
  btn.addEventListener('click', () => {
    const item   = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');

    // Tutup semua item dulu
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));

    // Buka item yang diklik jika sebelumnya tertutup
    if (!isOpen) item.classList.add('open');
  });
});