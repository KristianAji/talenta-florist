// js/katalog.js – Filter & variant selector untuk halaman katalog

// ── FILTER TABS ──
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const kat = btn.dataset.kat;

    document.querySelectorAll('.product-card').forEach((card, i) => {
      const match = kat === 'semua' || card.dataset.kat === kat;
      card.style.transition = `opacity .35s ${i * 40}ms, transform .35s ${i * 40}ms`;
      if (match) {
        card.style.display = 'flex';
        requestAnimationFrame(() => card.classList.add('show'));
      } else {
        card.classList.remove('show');
        setTimeout(() => { card.style.display = 'none'; }, 380);
      }
    });
  });
});

// ── VARIANT SELECTOR ──
window.selectVariant = function(btn, productId, variantIndex) {
  const card    = btn.closest('.product-card');
  const data    = JSON.parse(card.dataset.variants);
  const variant = data[variantIndex];

  // Tombol aktif
  card.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  // Harga
  const priceEl = card.querySelector('.product-price');
  if (priceEl) priceEl.textContent = 'Rp ' + Number(variant.harga).toLocaleString('id-ID');

  // Label gambar
  const labelEl = card.querySelector('.variant-label-img');
  if (labelEl) labelEl.textContent = variant.nama;

  // WhatsApp link
  const waEl = card.querySelector('.wa-btn');
  if (waEl) {
    const name = card.querySelector('.product-name').textContent;
    const txt  = `Halo Kios Bunga Talenta, saya tertarik dengan produk *${name}* variasi *${variant.nama}* (Rp ${Number(variant.harga).toLocaleString('id-ID')}). Apakah masih tersedia?`;
    waEl.href  = `https://wa.me/6285233608339?text=${encodeURIComponent(txt)}`;
  }

  // Ganti gambar dengan fade
  const imgEl = card.querySelector('.product-img img');
  if (imgEl && variant.gambar) {
    imgEl.style.opacity   = '0';
    imgEl.style.transform = 'scale(.97)';
    setTimeout(() => {
      imgEl.src = '/talenta-florist/' + variant.gambar;
      imgEl.onload = () => { imgEl.style.opacity = '1'; imgEl.style.transform = 'scale(1)'; };
      imgEl.onerror= () => { imgEl.style.opacity = '1'; imgEl.style.transform = 'scale(1)'; };
    }, 200);
  }
};

// ── ENTRANCE ANIMATION ──
requestAnimationFrame(() => {
  document.querySelectorAll('.product-card').forEach((card, i) => {
    card.style.transition = `opacity .45s ${i * 70}ms cubic-bezier(.22,1,.36,1), transform .45s ${i * 70}ms cubic-bezier(.22,1,.36,1), box-shadow .35s`;
    requestAnimationFrame(() => card.classList.add('show'));
  });
});