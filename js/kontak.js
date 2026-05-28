// js/kontak.js

// Staggered contact cards entrance
const cards   = document.querySelectorAll('.contact-card');
const cardObs = new IntersectionObserver(entries => {
  if (entries[0].isIntersecting) {
    cards.forEach((c, i) => {
      c.style.transition = `opacity .5s ${i * 120}ms cubic-bezier(.22,1,.36,1), transform .5s ${i * 120}ms cubic-bezier(.22,1,.36,1), background .25s, box-shadow .3s`;
      setTimeout(() => c.classList.add('show'), i * 120);
    });
    cardObs.disconnect();
  }
}, { threshold: .15 });

if (cards.length) cardObs.observe(document.querySelector('.contact-cards'));

// Map box reveal
const mapBox = document.getElementById('mapBox');
if (mapBox) {
  const mapObs = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) { mapBox.classList.add('show'); mapObs.disconnect(); }
  }, { threshold: .2 });
  mapObs.observe(mapBox);
}